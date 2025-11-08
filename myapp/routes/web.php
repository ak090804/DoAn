<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminProductVariantController;
use App\Http\Controllers\Admin\AdminUserController;


Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('categories', AdminCategoryController::class);
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('products', AdminProductController::class);
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('productVariants', AdminProductVariantController::class);
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', AdminUserController::class);
});






Route::get('/', function () {
    return view('ecommerce.index');
});




