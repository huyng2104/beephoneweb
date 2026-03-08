<?php

use App\Http\Controllers\AdminControllers\CategoryController;
use App\Http\Controllers\AdminControllers\CategoryFilterController;
use App\Http\Controllers\AdminControllers\BrandController;
use App\Http\Controllers\AdminControllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/', function () {
        return view('admin.dashboard.index');
    })->name('dashboard');


    // Vouchers
    Route::prefix('vouchers')->name('vouchers.')->group(function(){
        Route::get('/',function(){
            return view('admin.vouchers.index');
        })->name('index');
    });

});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('brands', BrandController::class)->except(['show']);

    Route::get('categories/{category}/filters', [CategoryFilterController::class, 'edit'])
        ->name('categories.filters.edit');
    Route::put('categories/{category}/filters', [CategoryFilterController::class, 'update'])
        ->name('categories.filters.update');
    Route::post('categories/{category}/filters/attributes', [CategoryFilterController::class, 'storeAttribute'])
        ->name('categories.filters.attributes.store');
    Route::patch('categories/{category}/filters/attributes/{attribute}/toggle', [CategoryFilterController::class, 'toggleFilterable'])
        ->name('categories.filters.attributes.toggle');
    Route::delete('categories/{category}/filters/attributes/{attribute}', [CategoryFilterController::class, 'detachAttribute'])
        ->name('categories.filters.attributes.detach');

    Route::get('products/create', [ProductController::class, 'create'])
        ->name('products.create');
    Route::post('products', [ProductController::class, 'store'])
        ->name('products.store');
});
    Route::prefix('users')->name('users.')->group(function(){

        Route::get('/', [UserController::class, 'index'])->name('index');

        Route::get('/create', [UserController::class, 'create'])->name('create');

        Route::post('/', [UserController::class, 'store'])->name('store');

        Route::get('/trash', [UserController::class, 'trash'])->name('trash');

        Route::get('/{user}', [UserController::class, 'show'])->name('show');

        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');

        Route::put('/{user}', [UserController::class, 'update'])->name('update');

        Route::post('/{user}/lock', [UserController::class, 'toggleLock'])->name('lock');

        Route::post('/{user}/restore', [UserController::class, 'restore'])->name('restore');

        Route::delete('/{user}/force-delete', [UserController::class, 'forceDelete'])->name('forceDelete');

        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

