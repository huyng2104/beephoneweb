<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminControllers\PostController;
use App\Http\Controllers\AdminControllers\PostCategoryController;


// Client
Route::get('/', function () {
    return view('welcome');
});





// Admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard.index');
    })->name('dashboard');

    Route::resource('posts', PostController::class)->except(['show']);
    Route::resource('post-categories', PostCategoryController::class)->except(['show']);
});
