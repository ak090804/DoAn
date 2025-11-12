<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminProductVariantController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Client\ClientHomeController;
use App\Http\Controllers\Client\ClientProductController;


Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('products', AdminProductController::class);
    Route::resource('productVariants', AdminProductVariantController::class);
    Route::resource('users', AdminUserController::class);
    Route::resource('employees', EmployeeController::class);
    Route::resource('customers', App\Http\Controllers\Admin\CustomersController::class);
    Route::resource('suppliers', App\Http\Controllers\Admin\AdminSupplierController::class);

    // Orders routes
    Route::resource('orders', AdminOrderController::class);
    Route::patch('orders/{order}/update-status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

    // Import notes routes
    Route::resource('import-notes', App\Http\Controllers\Admin\AdminImportNoteController::class);
});

Route::get('/', function () {
    return view('ecommerce.index');
});

// Authentication routes (simple session-based auth)
use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Account page
Route::get('/account', [AuthController::class, 'account'])->name('account');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Client-side routes
Route::get('/', [ClientHomeController::class, 'home'])->name('client.home');
Route::get('/products', [ClientProductController::class, 'products'])->name('client.products');
Route::get('/product/{id}', [ClientProductController::class, 'show'])->name('client.productDetail');

// Cart routes
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\CheckoutController;
Route::get('/cart', [CartController::class, 'viewCart'])->name('client.cart');
Route::post('/api/cart/add', [CartController::class, 'addToCart'])->name('api.cart.add');
Route::post('/api/cart/update', [CartController::class, 'updateQuantity'])->name('api.cart.update');
Route::post('/api/cart/remove', [CartController::class, 'removeFromCart'])->name('api.cart.remove');
Route::get('/api/cart/data', [CartController::class, 'getCartData'])->name('api.cart.data');
Route::post('/api/cart/clear', [CartController::class, 'clearCart'])->name('api.cart.clear');
Route::get('/api/cart/check-login', [CartController::class, 'checkLogin'])->name('api.cart.checkLogin');

// Checkout routes
Route::get('/checkout', [CheckoutController::class, 'show'])->name('client.checkout');
Route::post('/checkout/store', [CheckoutController::class, 'store'])->name('client.checkout.store');
Route::get('/checkout/confirm/{order}', [CheckoutController::class, 'confirm'])->name('client.orderConfirm');





