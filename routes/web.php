<?php

use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\AuthorController;
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


Route::controller(AuthorController::class)->group(function () {

    // All authors
    Route::get('/authors', 'index')->name('authors.index');

    // Create new author
    Route::get('/authors/create', 'create')->name('authors.create');
    Route::post('/authors', 'store')->name('authors.store');

    // Must be before dynamic {author}
    Route::get('/authors/{author}/edit', 'edit')->name('authors.edit');
    Route::put('/authors/{author}', 'update')->name('authors.update');
    Route::delete('/authors/{author}', 'destroy')->name('authors.destroy');

    // View single author
    Route::get('/authors/{author}', 'show')->name('authors.show');
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
