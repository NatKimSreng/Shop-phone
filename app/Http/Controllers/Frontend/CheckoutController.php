<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use KHQR\BakongKHQR;
use KHQR\Helpers\KHQRData;
use KHQR\Models\IndividualInfo;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CheckoutController extends Controller
{
    /**
     * Display the checkout page.
     */
    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty. Add some products before checkout.');
        }

        // Validate cart
        $validatedCart = [];
        $errors = [];

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);

            if (!$product) {
                $errors[] = "Product '{$item['name']}' is no longer available.";
                continue;
            }

            if (!$product->stock) {
                $errors[] = "Product '{$item['name']}' is out of stock.";
                continue;
            }

            $validatedCart[$productId] = [
                'name'   => $product->name,
                'price'  => $product->price,
                'image'  => $product->image,
                'qty'    => $item['qty'],
                'md5'    => null,
                'product'=> $product,
            ];
        }

        if (!empty($errors)) {
            session()->put('cart', $validatedCart);

            return redirect()->route('cart.index')
                ->withErrors(['cart' => $errors])
                ->with('error', 'Some items in your cart are no longer available.');
        }

        // Update cart session
        $cartData = [];
        foreach ($validatedCart as $id => $item) {
            $cartData[$id] = [
                'name'  => $item['name'],
                'price' => $item['price'],
                'image' => $item['image'],
                'qty'   => $item['qty'],
                'md5'   => null,
            ];
        }
        session()->put('cart', $cartData);

        // Calculate subtotal
        $subtotal = 0;
        foreach ($validatedCart as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }

        $user = Auth::check() ? Auth::user() : null;

        return view('frontend.checkout.index', compact('cart', 'subtotal', 'user'));
    }

    /**
     * Process checkout + create order.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'          => 'required|string|max:255',
                'email'         => 'required|email|max:255',
                'phone'         => 'required|string|max:20',
                'address'       => 'required|string|max:500',
                'city'          => 'nullable|string|max:100',
                'postal_code'   => 'nullable|string|max:20',
                'country'       => 'nullable|string|max:100',
                'notes'         => 'nullable|string|max:1000',
                'payment_method'=> 'required|in:cod,khqr',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Your cart is empty.'
                ], 400);
            }
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Validate products again
        $products = [];
        $subtotal = 0;
        $errors = [];

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);

            if (!$product || !$product->stock || $item['qty'] < 1) {
                $errors[] = "Product '{$item['name']}' is no longer available.";
                continue;
            }

            $itemSubtotal = $product->price * $item['qty'];
            $subtotal += $itemSubtotal;

            $products[] = compact('product', 'item', 'itemSubtotal');
        }

        if (!empty($errors)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => true,
                    'message' => implode(', ', $errors),
                    'errors' => $errors
                ], 400);
            }
            return back()->withErrors(['cart' => $errors])->withInput();
        }

        try {
            DB::beginTransaction();

            // Build order data array, only including fields that exist
            $orderData = [
                'user_id'       => Auth::check() ? Auth::id() : null,
                'name'          => $validated['name'],
                'email'         => $validated['email'],
                'phone'         => $validated['phone'],
                'address'       => $validated['address'],
                'total'         => $subtotal,
                'status'        => 'pending',
                'payment_method'=> $validated['payment_method'],
            ];

            // Add optional fields if they exist in the database
            if (Schema::hasColumn('orders', 'order_number')) {
                $orderData['order_number'] = Order::generateOrderNumber();
            }
            if (Schema::hasColumn('orders', 'subtotal')) {
                $orderData['subtotal'] = $subtotal;
            }
            if (Schema::hasColumn('orders', 'city')) {
                $orderData['city'] = $validated['city'] ?? null;
            }
            if (Schema::hasColumn('orders', 'postal_code')) {
                $orderData['postal_code'] = $validated['postal_code'] ?? null;
            }
            if (Schema::hasColumn('orders', 'country')) {
                $orderData['country'] = $validated['country'] ?? 'Cambodia';
            }
            if (Schema::hasColumn('orders', 'notes')) {
                $orderData['notes'] = $validated['notes'] ?? null;
            }

            $order = Order::create($orderData);

            // Save each order item
            foreach ($products as $p) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $p['product']->id,
                    'qty'        => $p['item']['qty'],
                    'price'      => $p['product']->price,
                    'subtotal'   => $p['itemSubtotal'],
                ]);
            }

            // If KHQR payment — generate MD5 + KHQR
            $result = null;
            $amountInKHR = 0;

            if ($validated['payment_method'] === 'khqr') {
                try {
                    // Convert USD to KHR (1 USD = 4100 KHR)
                    $amountInKHR = (int)round($subtotal * 4100);

                    $info = new IndividualInfo(
                        bakongAccountID: 'nat_kimsreng@aclb',
                        merchantName:    'NAT KIMSRENG',
                        merchantCity:    'Phnom Penh',
                        currency:        KHQRData::CURRENCY_KHR,
                        amount:          $amountInKHR,
                        billNumber:      'ORD-' . $order->id,
                        storeLabel:      'Online Shop',
                        mobileNumber:    '85581477716',
                    );

                    $result = BakongKHQR::generateIndividual($info);

                    // Save MD5 hash
                    $order->khqr_md5 = $result->data['md5'] ?? null;
                    $order->save();
                } catch (\Exception $khqrError) {
                    Log::error('KHQR generation failed: ' . $khqrError->getMessage());
                    throw new \Exception('Failed to generate payment QR code: ' . $khqrError->getMessage());
                }
            }

            DB::commit();
            session()->forget('cart');

            // If KHQR, return JSON QR code response
            if ($validated['payment_method'] === 'khqr' && $result) {
                // Set session for guest access to success page
                session(['last_order_id' => $order->id]);

                $khqrString = $result->data['qr'];

                $svg = QrCode::format('svg')
                    ->size(320)
                    ->margin(1)
                    ->generate($khqrString);

                return response()->json([
                    'qrCode'  => base64_encode($svg),
                    'amount'  => $amountInKHR,
                    'amountUSD' => round($subtotal, 2),
                    'orderId' => $order->id,
                ]);
            }

            // COD fallback - set session for guest access
            session(['last_order_id' => $order->id]);
            return redirect()->route('order.success', $order->id)
                ->with('success', 'Order placed successfully!');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error('Checkout SQL error: ' . $e->getMessage(), [
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'trace' => $e->getTraceAsString()
            ]);

            $errorMessage = 'Database error occurred. Please contact support.';

            // If AJAX request, return JSON error
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => true,
                    'message' => $errorMessage,
                    'debug' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

            return back()->with('error', $errorMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // If AJAX request, return JSON error
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Payment failed: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Payment failed. Try again.');
        }
    }

    /**
     * Check KHQR payment status using MD5.
     */
    public function paymentStatus(Order $order)
{
    if ($order->payment_method !== 'khqr' || empty($order->khqr_md5)) {
        return response()->json(['paid' => $order->status === 'paid']);
    }

    // If already paid, return immediately
    if ($order->status === 'paid') {
        return response()->json(['paid' => true, 'responseCode' => 0]);
    }

        // IMPORTANT: Only simulate in development/testing - NEVER in production
    $simulate = filter_var(config('services.bakong.simulate_success', false), FILTER_VALIDATE_BOOLEAN);
        if ($simulate && config('app.debug')) {
            Log::warning('SIMULATION MODE: Auto-marking order as paid (ONLY FOR TESTING)', [
                'order_id' => $order->id,
                'md5' => $order->khqr_md5
            ]);
        $order->update(['status' => 'paid', 'paid_at' => now()]);

        // Refresh order to ensure all data is loaded
        $order->refresh();
        $order->load('orderItems.product');

        // Send Telegram notification for simulated payment
        try {
            Log::info('Attempting to send Telegram notification (simulated) for order #' . $order->id);
            $result = \App\Http\Controllers\TelegramBotController::sendOrderNotification($order);
            if ($result) {
                Log::info('Telegram notification sent successfully (simulated) for order #' . $order->id);
            } else {
                Log::warning('Telegram notification returned false (simulated) for order #' . $order->id);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram notification (simulated): ' . $e->getMessage(), [
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString()
            ]);
        }

        return response()->json(['paid' => true, 'simulated' => true]);
    }

    $token = config('services.bakong.token');
    if (empty($token)) {
            Log::warning('KHQR payment check failed: No API token configured', ['order_id' => $order->id]);
            return response()->json(['paid' => false, 'error' => 'Payment verification unavailable']);
    }

    try {
        $client = new \KHQR\BakongKHQR($token);
        $isTest = filter_var(config('services.bakong.test_mode', true), FILTER_VALIDATE_BOOLEAN);

        $resp = $client->checkTransactionByMD5($order->khqr_md5, $isTest);

            // Log the full response for debugging
            Log::info('KHQR payment check response', [
                'order_id' => $order->id,
                'md5' => $order->khqr_md5,
                'response' => $resp
            ]);

            // Parse response more carefully
            $data = $resp['data'] ?? [];

            // Check responseCode at top level first (this is the main indicator)
            $responseCode = isset($resp['responseCode']) ? (int) $resp['responseCode'] : null;
            $responseMessage = $resp['responseMessage'] ?? '';

            // Check for status in various places
            $status = $data['transactionStatus']
                ?? $data['status']
                ?? $resp['transactionStatus']
                ?? $resp['status']
                ?? null;

            $statusUpper = strtoupper(trim((string) $status));
            $messageUpper = strtoupper(trim((string) $responseMessage));

            // Primary check: responseCode === 0 means success
            $paid = false;
            if ($responseCode === 0) {
                // Success confirmed by responseCode
                $paid = true;
                Log::info('Payment confirmed by responseCode: 0', [
                    'order_id' => $order->id,
                    'responseMessage' => $responseMessage
                ]);
            } elseif ($responseCode !== null && $responseCode !== 0) {
                // Explicit failure
                $paid = false;
            } elseif (in_array($statusUpper, ['SUCCESS', 'SUCCESSFUL', 'COMPLETED']) ||
                      in_array($messageUpper, ['SUCCESS', 'SUCCESSFUL'])) {
                // Fallback: check status/message strings
                $paid = true;
            }

            // Double-check: if status is explicitly FAILED, PENDING, or CANCELLED, it's NOT paid
            if (in_array($statusUpper, ['FAILED', 'PENDING', 'CANCELLED', 'CANCEL', 'ERROR']) ||
                in_array($messageUpper, ['FAILED', 'ERROR', 'CANCELLED'])) {
                $paid = false;
            }

            // Only update if we're CERTAIN it's paid
        if ($paid && $order->status !== 'paid') {
                Log::info('KHQR payment confirmed', [
                    'order_id' => $order->id,
                    'status' => $status,
                    'md5' => $order->khqr_md5
                ]);

            $order->update(['status' => 'paid', 'paid_at' => now()]);

            // Refresh order to ensure all data is loaded
            $order->refresh();
            $order->load('orderItems.product');

            // Send Telegram notification
            try {
                Log::info('Attempting to send Telegram notification for order #' . $order->id);
                $result = \App\Http\Controllers\TelegramBotController::sendOrderNotification($order);
                if ($result) {
                    Log::info('Telegram notification sent successfully for order #' . $order->id);
                } else {
                    Log::warning('Telegram notification returned false for order #' . $order->id);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send Telegram notification: ' . $e->getMessage(), [
                    'order_id' => $order->id,
                    'trace' => $e->getTraceAsString()
                ]);
            }
            } else {
                Log::info('KHQR payment NOT confirmed', [
                    'order_id' => $order->id,
                    'status' => $status,
                    'status_upper' => $statusUpper,
                    'paid' => $paid
                ]);
        }

            // Return response with responseCode for frontend checking (like the example)
            $responseCode = $data['responseCode'] ?? $resp['responseCode'] ?? null;
            return response()->json([
                'paid' => $paid,
                'status' => $status,
                'responseCode' => $responseCode,
                'data' => $data
            ]);

    } catch (\Throwable $e) {
            Log::error('KHQR polling failed', [
                'order_id' => $order->id,
                'md5' => $order->khqr_md5,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // On error, return false - never mark as paid on error
            return response()->json(['paid' => false, 'error' => 'Verification failed']);
    }
}

    /**
     * Manual fallback for marking KHQR order as paid.
     */
    public function manualComplete(Order $order)
    {
        if ($order->payment_method !== 'khqr') {
            return response()->json(['paid' => false], 400);
        }

        if ($order->status !== 'paid') {
            $order->update(['status' => 'paid', 'paid_at' => now()]);

            // Refresh order to ensure all data is loaded
            $order->refresh();
            $order->load('orderItems.product');

            // Send Telegram notification
            try {
                Log::info('Attempting to send Telegram notification (manual) for order #' . $order->id);
                $result = \App\Http\Controllers\TelegramBotController::sendOrderNotification($order);
                if ($result) {
                    Log::info('Telegram notification sent successfully (manual) for order #' . $order->id);
                } else {
                    Log::warning('Telegram notification returned false (manual) for order #' . $order->id);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send Telegram notification (manual): ' . $e->getMessage(), [
                    'order_id' => $order->id,
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        return response()->json(['paid' => true]);
    }
    /**
 * Show order success page with real-time payment status
 */
/**
 * Show order success page with REAL-TIME final payment check
 */
/**
 * Show order success page — with GUARANTEED final payment verification
 */
public function success(Order $order)
{
    // Load order items
    $order->load('orderItems.product');

    // === SECURITY CHECK ===
    // For authenticated users: must own the order
    if (Auth::check() && Auth::id() !== $order->user_id) {
        abort(403);
    }

    // For guest users: allow if session matches OR order was created recently (within 30 minutes)
    if (!Auth::check()) {
        $sessionOrderId = session('last_order_id');
        $orderAge = now()->diffInMinutes($order->created_at);

        // Allow if session matches OR order was created within last 30 minutes
        if ($sessionOrderId !== $order->id && $orderAge > 30) {
            abort(403);
        }
    }

    // === FINAL KHQR PAYMENT VERIFICATION (runs EVERY time page loads) ===
    if (
        $order->payment_method === 'khqr' &&
        $order->status !== 'paid' &&
        $order->khqr_md5 &&
        config('services.bakong.token') // only if token exists
    ) {
        try {
            $isTest = filter_var(config('services.bakong.test_mode', true), FILTER_VALIDATE_BOOLEAN);
            $simulate = filter_var(config('services.bakong.simulate_success', false), FILTER_VALIDATE_BOOLEAN);

            if ($simulate) {
                $order->update(['status' => 'paid', 'paid_at' => now()]);

                // Send Telegram notification for simulated payment
                try {
                    \App\Http\Controllers\TelegramBotController::sendOrderNotification($order);
                } catch (\Exception $e) {
                    Log::warning('Failed to send Telegram notification: ' . $e->getMessage());
                }
            } else {
                $token = config('services.bakong.token');
                if (empty($token)) {
                    Log::warning('KHQR verification skipped: No API token', ['order_id' => $order->id]);
                } else {
                    try {
                        $client = new \KHQR\BakongKHQR($token);
                $response = $client->checkTransactionByMD5($order->khqr_md5, $isTest);

                        // Log response for debugging
                        Log::info('KHQR success page check', [
                            'order_id' => $order->id,
                            'response' => $response
                        ]);

                        $data = $response['data'] ?? [];

                        // Check responseCode at top level first (this is the main indicator)
                        $responseCode = isset($response['responseCode']) ? (int) $response['responseCode'] : null;
                        $responseMessage = $response['responseMessage'] ?? '';

                        // Check for status in various places
                        $status = $data['transactionStatus']
                            ?? $data['status']
                            ?? $response['transactionStatus']
                            ?? $response['status']
                            ?? null;

                        $statusUpper = strtoupper(trim((string) $status));
                        $messageUpper = strtoupper(trim((string) $responseMessage));

                        // Primary check: responseCode === 0 means success
                        $paid = false;
                        if ($responseCode === 0) {
                            // Success confirmed by responseCode
                            $paid = true;
                        } elseif ($responseCode !== null && $responseCode !== 0) {
                            // Explicit failure
                            $paid = false;
                        } elseif (in_array($statusUpper, ['SUCCESS', 'SUCCESSFUL', 'COMPLETED']) ||
                                  in_array($messageUpper, ['SUCCESS', 'SUCCESSFUL'])) {
                            // Fallback: check status/message strings
                            $paid = true;
                        }

                        // Double-check: if status is explicitly FAILED, PENDING, or CANCELLED, it's NOT paid
                        if (in_array($statusUpper, ['FAILED', 'PENDING', 'CANCELLED', 'CANCEL', 'ERROR']) ||
                            in_array($messageUpper, ['FAILED', 'ERROR', 'CANCELLED'])) {
                            $paid = false;
                        }

                        if ($paid) {
                            Log::info('KHQR payment confirmed on success page', [
                                'order_id' => $order->id,
                                'status' => $status
                            ]);

                    $order->update(['status' => 'paid', 'paid_at' => now()]);

                    // Refresh order to ensure all data is loaded
                    $order->refresh();
                    $order->load('orderItems.product');

                    // Send Telegram notification
                    try {
                        Log::info('Attempting to send Telegram notification (success page) for order #' . $order->id);
                        $result = \App\Http\Controllers\TelegramBotController::sendOrderNotification($order);
                        if ($result) {
                            Log::info('Telegram notification sent successfully (success page) for order #' . $order->id);
                        } else {
                            Log::warning('Telegram notification returned false (success page) for order #' . $order->id);
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to send Telegram notification (success page): ' . $e->getMessage(), [
                            'order_id' => $order->id,
                            'trace' => $e->getTraceAsString()
                        ]);
                            }
                        } else {
                            Log::info('KHQR payment NOT confirmed on success page', [
                                'order_id' => $order->id,
                                'status' => $status,
                                'status_upper' => $statusUpper
                            ]);
                        }
                    } catch (\Throwable $e) {
                        Log::error('KHQR verification error on success page', [
                            'order_id' => $order->id,
                            'error' => $e->getMessage()
                        ]);
                        // Don't mark as paid on error
                    }
                }
            }

            $order->refresh(); // reload fresh data

        } catch (\Throwable $e) {
            Log::warning('Final Bakong check failed on success page', [
                'order_id' => $order->id,
                'md5'      => $order->khqr_md5,
                'error'    => $e->getMessage()
            ]);
            // Do NOT block success page — user already paid, just API hiccup
        }
    }

    // Allow guest to re-access success page
    session(['last_order_id' => $order->id]);

    return view('frontend.checkout.success', compact('order'));
}
}
