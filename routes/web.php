<?php

use App\Http\Controllers\AdminControllers\CategoryController;
use App\Http\Controllers\AdminControllers\CategoryFilterController;
use App\Http\Controllers\AdminControllers\BrandController;
use App\Http\Controllers\AdminControllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin', function () {
    return view('admin.dashboard.index');
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
