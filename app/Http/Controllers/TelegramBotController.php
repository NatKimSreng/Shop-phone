<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramBotController extends Controller
{
    /**
     * Handle Telegram webhook
     */
    public function handleWebhook(Request $request)
    {
        // This is for receiving messages from Telegram
        // You can implement bot commands here if needed
        return response()->json(['ok' => true]);
    }

    /**
     * Send order notification to Telegram
     */
    public static function sendOrderNotification(Order $order)
    {
        try {
            Log::info('sendOrderNotification called', [
                'order_id' => $order->id ?? 'N/A',
                'order_status' => $order->status ?? 'N/A'
            ]);

            $botToken = config('services.telegram.bot_token');
            $chatId = config('services.telegram.chat_id');

            if (empty($botToken) || empty($chatId)) {
                Log::warning('Telegram bot token or chat ID not configured', [
                    'has_token' => !empty($botToken),
                    'has_chat_id' => !empty($chatId),
                    'order_id' => $order->id ?? 'N/A'
                ]);
                return false;
            }

            // Refresh order to get latest data
            $order->refresh();

            // Ensure order items are loaded
            if (!$order->relationLoaded('orderItems')) {
                Log::info('Loading order items for order #' . $order->id);
                $order->load('orderItems.product');
            }

            // Double check order items exist
            if ($order->orderItems->isEmpty()) {
                Log::warning('Order has no items, cannot send notification', [
                    'order_id' => $order->id,
                    'order_status' => $order->status,
                    'order_items_count' => $order->orderItems->count()
                ]);
                return false;
            }

            Log::info('Preparing Telegram notification', [
                'order_id' => $order->id,
                'items_count' => $order->orderItems->count(),
                'total' => $order->total,
                'customer' => $order->name
            ]);

            // Format order details using HTML (more reliable than Markdown)
            $message = "🎉 <b>New Payment Received!</b>\n\n";
            $message .= "📦 <b>Order #</b>: " . htmlspecialchars($order->order_number ?? $order->id) . "\n";
            $message .= "💰 <b>Amount</b>: $" . number_format((float)$order->total, 2) . "\n";
            $message .= "💳 <b>Payment Method</b>: " . strtoupper(htmlspecialchars($order->payment_method ?? 'N/A')) . "\n";
            $message .= "👤 <b>Customer</b>: " . htmlspecialchars($order->name) . "\n";
            $message .= "📧 <b>Email</b>: " . htmlspecialchars($order->email) . "\n";
            $message .= "📱 <b>Phone</b>: " . htmlspecialchars($order->phone) . "\n";
            $message .= "📍 <b>Address</b>: " . htmlspecialchars($order->address) . "\n";

            if ($order->city) {
                $message .= "🏙️ <b>City</b>: " . htmlspecialchars($order->city) . "\n";
            }

            $message .= "\n📋 <b>Items:</b>\n";
            foreach ($order->orderItems as $item) {
                $productName = htmlspecialchars($item->product->name ?? "Product #{$item->product_id}");
                $itemTotal = $item->subtotal ?? ($item->price * $item->qty);
                $message .= "• {$productName} x{$item->qty} - $" . number_format((float)$itemTotal, 2) . "\n";
            }

            $message .= "\n⏰ <b>Order Date</b>: " . $order->created_at->format('Y-m-d H:i:s') . "\n";
            $message .= "✅ <b>Status</b>: " . ucfirst(htmlspecialchars($order->status));

            // Send message using direct HTTP API (more reliable)
            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

            $data = [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML', // Use HTML instead of Markdown for better compatibility
            ];

            // Try using Laravel HTTP client first (more reliable)
            if (function_exists('app') && class_exists('\Illuminate\Support\Facades\Http')) {
                try {
                    $response = \Illuminate\Support\Facades\Http::timeout(10)->post($url, $data);

                    if ($response->successful() && $response->json('ok')) {
                        Log::info('Telegram notification sent successfully for order #' . $order->id);
                        return true;
                    } else {
                        $errorMsg = $response->json('description') ?? 'Unknown error';
                        throw new \Exception("Telegram API Error: {$errorMsg}");
                    }
                } catch (\Exception $httpError) {
                    Log::warning('HTTP client failed, trying cURL: ' . $httpError->getMessage());
                }
            }

            // Fallback to cURL
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                Log::error('cURL Error details', [
                    'error' => $error,
                    'http_code' => $httpCode,
                    'response' => $response
                ]);
                throw new \Exception("cURL Error: {$error}");
            }

            $result = json_decode($response, true);

            if (!$result) {
                Log::error('Invalid JSON response from Telegram', [
                    'response' => $response,
                    'http_code' => $httpCode
                ]);
                throw new \Exception("Invalid response from Telegram API");
            }

            if ($httpCode !== 200 || !isset($result['ok']) || !$result['ok']) {
                $errorMsg = $result['description'] ?? ($result['error_code'] ?? 'Unknown error');
                Log::error('Telegram API Error', [
                    'http_code' => $httpCode,
                    'error_code' => $result['error_code'] ?? null,
                    'description' => $result['description'] ?? null,
                    'full_response' => $result
                ]);
                throw new \Exception("Telegram API Error: {$errorMsg}");
            }

            Log::info('Telegram notification sent successfully for order #' . $order->id, [
                'message_id' => $result['result']['message_id'] ?? null
            ]);
            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send Telegram notification: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}

