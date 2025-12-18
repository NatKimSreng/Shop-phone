<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\CheckoutController as FrontendCheckoutController;
use App\Http\Controllers\admin\orders;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/category/{slug}', [CategoryController::class, 'show'])
    ->name('category.show');

Route::get('/search', [SearchController::class, 'index'])
    ->name('search');

// Optional: redirect old /login to new admin login
Route::redirect('/login', '/admin/login')->name('login');

/*
|--------------------------------------------------------------------------
| Admin Panel Routes – Prefixed with /admin
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    // ==================== Guest Routes (Login) ====================
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'login'])
            ->name('login'); // → route: admin.login

        Route::post('/login', [AuthController::class, 'authenticate']);
    });

    // ==================== Authenticated Admin Routes ====================
    Route::middleware(['auth', 'admin'])->group(function () {

        // Logout
        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('logout');

        // Dashboard
        Route::get('/', [\App\Http\Controllers\admin\DashboardController::class, 'index'])
            ->name('dashboard');

        // Products Resource Routes
        // → /admin/products, /admin/products/create, /admin/products/{product}, etc.
        Route::resource('products', ProductController::class)
            ->parameters(['products' => 'product']);

        // Order Import/Export Routes (MUST be before resource route to avoid conflicts)
        Route::get('/orders/export', [orders::class, 'export'])
            ->name('orders.export');
        Route::post('/orders/import', [orders::class, 'import'])
            ->name('orders.import');
        Route::get('/orders/import/template', [orders::class, 'downloadTemplate'])
            ->name('orders.import.template');

        // Orders Resource Routes (after specific routes)
        Route::resource('orders', orders::class)
            ->parameters(['orders' => 'order']);

        // Categories Resource Routes
        Route::resource('categories', CategoryController::class)
            ->parameters(['categories' => 'category']);

        // Authors Resource Routes
        Route::resource('authors', AuthorController::class)
            ->parameters(['authors' => 'author']);
    });
});

/*
|--------------------------------------------------------------------------
| Registration Routes (if you still allow public registration)
|--------------------------------------------------------------------------
| If you don't want public registration, just delete these.
*/
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'register'])
        ->name('auth.register');

    Route::post('/register', [AuthController::class, 'store'])
        ->name('register.store');
});
Route::get('/products', [\App\Http\Controllers\Frontend\ProductController::class, 'index'])
    ->name('frontend.products.index');

Route::get('/products/{product}', [\App\Http\Controllers\Frontend\ProductController::class, 'show'])
    ->name('frontend.products.show');
// CART
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::get('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
// CHECKOUT

// Order Success Page
Route::get('/order/success/{order}', [CheckoutController::class, 'success'])
    ->name('order.success');

// User Order History (Authenticated Users Only)
Route::middleware('auth')->group(function () {
    Route::get('/orders', [\App\Http\Controllers\Frontend\OrderHistoryController::class, 'index'])
        ->name('orders.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\Frontend\OrderHistoryController::class, 'show'])
        ->name('orders.show');
});

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/payment/status/{order}', [CheckoutController::class, 'paymentStatus'])
     ->name('payment.status');
// In routes/web.php
Route::post('/payment/manual-complete/{order}', [CheckoutController::class, 'manualComplete'])
     ->name('payment.manual-complete');
Route::post('/telegram/webhook', [\App\Http\Controllers\TelegramBotController::class, 'handleWebhook']);

// Test Telegram notification (for debugging)
Route::get('/telegram/test', function() {
    $botToken = config('services.telegram.bot_token');
    $chatId = config('services.telegram.chat_id');

    if (empty($botToken) || empty($chatId)) {
        return response()->json([
            'error' => 'Telegram not configured',
            'has_token' => !empty($botToken),
            'has_chat_id' => !empty($chatId)
        ], 400);
    }

    $testMessage = "🧪 <b>Test Message</b>\n\nThis is a test from your Laravel application.\n\nIf you see this, Telegram is working! ✅";

    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $data = [
        'chat_id' => $chatId,
        'text' => $testMessage,
        'parse_mode' => 'HTML',
    ];

    try {
        if (class_exists('\Illuminate\Support\Facades\Http')) {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->post($url, $data);
            return response()->json([
                'success' => $response->successful(),
                'response' => $response->json(),
                'status' => $response->status()
            ]);
        } else {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return response()->json([
                'success' => $httpCode === 200,
                'response' => json_decode($response, true),
                'status' => $httpCode
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
})->name('telegram.test');
