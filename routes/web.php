<?php

use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\auth\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Route::get('/', function () {
//     return view('welcome');
// });
// Route::get('/', function () {
//     return view('products.index');
// });

// Route group controller
// 

// Route::controller(ProductController::class)->group(function () {
//     Route::get('/products', 'index')->name('products.index');
//     Route::get('/products/create', 'create')->name('products.create');
//     Route::post('/products', 'store')->name('products.store');

//     // Specific routes before dynamic {product}
//     Route::get('/products/{product}/edit', 'edit')->name('products.edit');
//     Route::put('/products/{product}', 'update')->name('products.update');
//     Route::delete('/products/{product}', 'destroy')->name('products.destroy');
//     Route::get('/products/{product}', 'show')->name('products.show');
//     // Route::resource('products', ProductController::class)->except(['index']); short hand 
// });

// Explicit routes for products (no resource or group—simple and clear)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');


Route::controller(CategoryController::class)->group(function () {
    Route::get('/categories', 'index')->name('categories.index');
    Route::get('/categories/create', 'create')->name('categories.create');
    Route::post('/categories', 'store')->name('categories.store');

    // Specific routes before dynamic {category}
    Route::get('/categories/{category}/edit', 'edit')->name('categories.edit');
    Route::put('/categories/{category}', 'update')->name('categories.update');
    Route::delete('/categories/{category}', 'destroy')->name('categories.destroy');
    Route::get('/categories/{category}', 'show')->name('categories.show');
});


Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('auth.login');
    Route::post('/login', 'authenticate')->name('login.store');

    Route::get('/register', 'register')->name('auth.register');
    Route::post('/register', 'store')->name('register.store');

    // ✅ Logout route
    Route::post('/logout', 'logout')->name('logout');
});
// Login page (GET)
// Route::get('/login', [AuthController::class, 'login'])->name('auth.login');

// // Login form submission (POST)
// Route::post('/login', [AuthController::class, 'authenticate'])->name('login.store');

// // Register page (GET)
// Route::get('/register', [AuthController::class, 'register'])->name('auth.register');

// // Register form submission (POST)
// Route::post('/register', [AuthController::class, 'store'])->name('register.store');

// // Logout
// Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Product page handled by ProductController@index (auth enforced there)

// // Default route
// Route::get('/', function () {
//     return redirect()->route('auth.login');
// });

