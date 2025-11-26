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
use App\Http\Controllers\AuthController as MainAuthController;


// Admin-specific login routes (separate from client login)
Route::get('/admin/login', [MainAuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [MainAuthController::class, 'adminLogin'])->name('admin.login.post');
Route::post('/admin/logout', [MainAuthController::class, 'adminLogout'])->name('admin.logout');

Route::prefix('admin')->name('admin.')->middleware('admin.role')->group(function () {
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
    Route::patch('import-notes/{id}/approve', [App\Http\Controllers\Admin\AdminImportNoteController::class, 'approve'])
        ->name('import-notes.approve')
        ->middleware('is.admin');
});

Route::get('/', function () {
    return view('ecommerce.index');
});

// Authentication routes (simple session-based auth)
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailVerificationController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [EmailVerificationController::class, 'register'])->name('register.post');

// Email verification routes
Route::post('/register/send-code', [EmailVerificationController::class, 'sendCode'])->name('register.send-code');
Route::post('/register/verify-code', [EmailVerificationController::class, 'verifyCode'])->name('register.verify-code');

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
Route::post('/api/voucher/verify', [CartController::class, 'verifyVoucher'])->name('api.voucher.verify');

// Checkout routes
Route::get('/checkout', [CheckoutController::class, 'show'])->name('client.checkout');
Route::post('/checkout/store', [CheckoutController::class, 'store'])->name('client.checkout.store');
Route::get('/checkout/confirm/{order}', [CheckoutController::class, 'confirm'])->name('client.orderConfirm');

// Client orders (history)
use App\Http\Controllers\Client\ClientOrderController;
Route::get('/account/orders', [ClientOrderController::class, 'index'])->name('client.orders.index');
Route::get('/account/orders/{id}', [ClientOrderController::class, 'show'])->name('client.orders.show');
Route::post('/account/orders/{id}/update-status', [ClientOrderController::class, 'updateStatus'])->name('client.orders.updateStatus');





