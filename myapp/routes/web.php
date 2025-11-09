<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminProductVariantController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\AdminOrderController;


Route::prefix('admin')->name('admin.')->group(function () {
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




