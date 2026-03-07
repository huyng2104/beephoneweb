<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminControllers\CategoryController;
use App\Http\Controllers\AdminControllers\CategoryFilterController;
use App\Http\Controllers\AdminControllers\BrandController;
use App\Http\Controllers\AdminControllers\ProductController;
use App\Http\Controllers\AdminControllers\AttributeController;
use App\Http\Controllers\AdminControllers\AttributeValueController;
use App\Http\Controllers\AdminControllers\VoucherController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Client
Route::get('/', function () {
    return view('welcome');
});





// Admin
Route::prefix('admin')->name('admin.')->group(function () {


    Route::get('/', function () {
        return view('admin.dashboard.index');
    })->name('dashboard');
});

Route::prefix('admin')->name('admin.')->group(function () {

    // 1. Quản lý Danh mục & Thương hiệu
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('brands', BrandController::class)->except(['show']);

    // 2. Quản lý Bộ lọc theo Danh mục
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


    // ==========================================
    // 3. QUẢN LÝ THUỘC TÍNH GỐC (ATTRIBUTES)
    // ==========================================

    // Thùng rác Thuộc tính (BẮT BUỘC ĐẶT TRƯỚC RESOURCE)
    Route::get('attributes/trash', [AttributeController::class, 'trash'])->name('attributes.trash');
    Route::post('attributes/{id}/restore', [AttributeController::class, 'restore'])->name('attributes.restore');
    Route::delete('attributes/{id}/force-delete', [AttributeController::class, 'forceDelete'])->name('attributes.force_delete');

    // Resource Thuộc tính
    Route::resource('attributes', AttributeController::class)->except(['create', 'show', 'edit']);


    // ==========================================
    // 4. QUẢN LÝ GIÁ TRỊ THUỘC TÍNH (ATTRIBUTE VALUES - TERMS)
    // ==========================================

    // Thùng rác Giá trị thuộc tính (BẮT BUỘC ĐẶT TRƯỚC)
    Route::get('attributes/{attribute}/values/trash', [AttributeValueController::class, 'trash'])->name('attributes.values.trash');
    Route::post('attribute-values/{id}/restore', [AttributeValueController::class, 'restore'])->name('attributes.values.restore');
    Route::delete('attribute-values/{id}/force-delete', [AttributeValueController::class, 'forceDelete'])->name('attributes.values.force_delete');

    // Các thao tác cơ bản với Giá trị thuộc tính
    Route::get('attributes/{attribute}/values', [AttributeValueController::class, 'index'])->name('attributes.values.index');
    Route::post('attributes/{attribute}/values', [AttributeValueController::class, 'store'])->name('attributes.values.store');
    Route::get('attributes/{attribute}/values/{attribute_value}/edit', [AttributeValueController::class, 'edit'])->name('attributes.values.edit');
    Route::put('attributes/{attribute}/values/{attribute_value}', [AttributeValueController::class, 'update'])->name('attributes.values.update');
    Route::delete('attribute-values/{attribute_value}', [AttributeValueController::class, 'destroy'])->name('attributes.values.destroy');
    // API Lấy giá trị thuộc tính cho AJAX
// API Lấy giá trị thuộc tính cho AJAX (Đổi thành get-values)
Route::get('/attributes/{id}/get-values', [\App\Http\Controllers\AdminControllers\AttributeController::class, 'getValues'])->name('admin.attributes.getValues');    Route::get('products/trash', [App\Http\Controllers\AdminControllers\ProductController::class, 'trash'])->name('products.trash');
    Route::post('products/{id}/restore', [App\Http\Controllers\AdminControllers\ProductController::class, 'restore'])->name('products.restore');
    Route::delete('products/{id}/force-delete', [App\Http\Controllers\AdminControllers\ProductController::class, 'forceDelete'])->name('products.force_delete');

    // Resource Sản phẩm
    Route::resource('products', App\Http\Controllers\AdminControllers\ProductController::class);

    // Vouchers
    // Nên đặt custom route TRƯỚC resource route để tránh xung đột
// 1. Thêm {id} vào đường dẫn để nhận $voucher->id từ view
// 2. Đổi tên route thành admin.vouchers.restore
// Bỏ chữ 'admin.' đi, chỉ giữ lại 'vouchers.restore'
Route::post('vouchers/{id}/restore', [VoucherController::class, 'restore'])->name('vouchers.restore');

Route::resource('vouchers', VoucherController::class);
});
