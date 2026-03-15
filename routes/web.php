<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminControllers\CategoryController;
use App\Http\Controllers\AdminControllers\CategoryFilterController;
use App\Http\Controllers\AdminControllers\BrandController;
use App\Http\Controllers\AdminControllers\ProductController;
use App\Http\Controllers\AdminControllers\AttributeController;
use App\Http\Controllers\AdminControllers\AttributeValueController;
use App\Http\Controllers\AuthControllers\AuthController;
use App\Http\Controllers\AdminControllers\VoucherController;
use App\Http\Controllers\AdminControllers\UserController;
use App\Http\Controllers\AdminControllers\OrderController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminControllers\PostController;
use App\Http\Controllers\AdminControllers\PostCategoryController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminControllers\WalletController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Client
Route::get('/', function () {
    return view('welcome');
})->middleware('check.verified')->name('/');

// Trang Đăng Nhập / Đăng ký
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('login/post', [AuthController::class, 'postLogin'])->name('login.post');
Route::post('register/post', [AuthController::class, 'postRegister'])->name('register.post');
Route::get('logout', [AuthController::class, 'logOut'])->name('logout');
// Quên mật khẩu
Route::get('reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
Route::post('post-reset-password', [AuthController::class, 'postResetPassword'])->name('post-reset-password');
Route::get('verify-code', [AuthController::class, 'verify_code'])->name('verify-code');
Route::post('check-otp', [AuthController::class, 'check_otp'])->name('check_otp');
// Kiểm tra xác thực email chưa
Route::get('/email/verify', function () {
    if (Auth::user() && Auth::user()->hasVerifiedEmail()) {
        return redirect()->route('/')->with([
            'success' => 'Đăng nhập thành công'
        ]);
    }
    return view('auth.verify_email.index');
})->name('verification.notice');

// Xác thực email
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('/')->with([
        'success' => 'Xác minh email thành công'
    ]);
})->middleware(['auth', 'signed'])->name('verification.verify');

// Gửi lại mã xác thực
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('success', 'Đã gửi lại email xác minh!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');



// ==========================================
// HỆ THỐNG ADMIN
// ==========================================
Route::middleware(['auth', 'verified', 'role:admin,staff', 'can:order.view'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {

        // Bảng điều khiển
        Route::get('/', function () {
            return view('admin.dashboard.index');
        })->name('dashboard');

        // ĐÃ FIX: Chuyển Users vào trong group admin. Dùng resource để tự động tạo name admin.users.*
        Route::resource('users', UserController::class);
        Route::post('user/{id}/block', [UserController::class, 'block'])->name('user.block');
        Route::post('user/{id}/unblock', [UserController::class, 'unBlock'])->name('user.unblock');
        Route::post('user/{id}/reset', [UserController::class, 'resetPw'])->name('resetPw');

        // 1. Quản lý Danh mục & Thương hiệu
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('brands', BrandController::class)->except(['show']);

        // 2. Quản lý Bộ lọc theo Danh mục
        Route::get('categories/{category}/filters', [CategoryFilterController::class, 'edit'])->name('categories.filters.edit');
        Route::put('categories/{category}/filters', [CategoryFilterController::class, 'update'])->name('categories.filters.update');
        Route::post('categories/{category}/filters/attributes', [CategoryFilterController::class, 'storeAttribute'])->name('categories.filters.attributes.store');
        Route::patch('categories/{category}/filters/attributes/{attribute}/toggle', [CategoryFilterController::class, 'toggleFilterable'])->name('categories.filters.attributes.toggle');
        Route::delete('categories/{category}/filters/attributes/{attribute}', [CategoryFilterController::class, 'detachAttribute'])->name('categories.filters.attributes.detach');

        Route::get('products/create', [ProductController::class, 'create'])
            ->name('products.create');
        Route::post('products', [ProductController::class, 'store'])
            ->name('products.store');

        // 3. Quản lý Thuộc tính gốc (Attributes)
        Route::get('attributes/trash', [AttributeController::class, 'trash'])->name('attributes.trash');
        Route::post('attributes/{id}/restore', [AttributeController::class, 'restore'])->name('attributes.restore');
        Route::delete('attributes/{id}/force-delete', [AttributeController::class, 'forceDelete'])->name('attributes.force_delete');
        Route::resource('attributes', AttributeController::class)->except(['create', 'show', 'edit']);

        // API Lấy giá trị thuộc tính cho AJAX (Đã fix lỗi dư tiền tố admin)
        Route::get('/attributes/{id}/get-values', [AttributeController::class, 'getValues'])->name('attributes.getValues');

        // 4. Quản lý Giá trị thuộc tính (Attribute Values)
        Route::get('attributes/{attribute}/values/trash', [AttributeValueController::class, 'trash'])->name('attributes.values.trash');
        Route::post('attribute-values/{id}/restore', [AttributeValueController::class, 'restore'])->name('attributes.values.restore');
        Route::delete('attribute-values/{id}/force-delete', [AttributeValueController::class, 'forceDelete'])->name('attributes.values.force_delete');

        Route::get('attributes/{attribute}/values', [AttributeValueController::class, 'index'])->name('attributes.values.index');
        Route::post('attributes/{attribute}/values', [AttributeValueController::class, 'store'])->name('attributes.values.store');
        Route::get('attributes/{attribute}/values/{attribute_value}/edit', [AttributeValueController::class, 'edit'])->name('attributes.values.edit');
        Route::put('attributes/{attribute}/values/{attribute_value}', [AttributeValueController::class, 'update'])->name('attributes.values.update');
        Route::delete('attribute-values/{attribute_value}', [AttributeValueController::class, 'destroy'])->name('attributes.values.destroy');

        // 5. Quản lý Sản phẩm
        Route::get('products/trash', [ProductController::class, 'trash'])->name('products.trash');
        Route::post('products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::delete('products/{id}/force-delete', [ProductController::class, 'forceDelete'])->name('products.force_delete');
        Route::resource('products', ProductController::class);

        // 6. Quản lý Vouchers
        Route::post('vouchers/{id}/restore', [VoucherController::class, 'restore'])->name('vouchers.restore');
        Route::resource('vouchers', VoucherController::class);
        // 7. Quản lý Đơn hàng
        Route::get('orders', [OrderController::class, 'index'])
            ->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])
            ->name('orders.show');
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])
            ->name('orders.status.update');
        Route::patch('orders/{order}/cancel', [OrderController::class, 'cancel'])
            ->name('orders.cancel');
        Route::patch('orders/{order}/return-confirm', [OrderController::class, 'confirmReturn'])
            ->name('orders.return.confirm');
        Route::get('orders/{order}/print-pdf', [OrderController::class, 'printPdf'])
            ->name('orders.print.pdf');

        // 8. Quản lý bài viết
        Route::resource('posts', PostController::class);
        Route::resource('post-categories', PostCategoryController::class);

        Route::post('posts/upload', [PostController::class, 'upload'])->name('posts.upload');


        // Quản lý Ví tiền
        Route::get('/wallets', [WalletController::class, 'index'])->name('wallets.index');
        Route::post('/wallets/update-balance', [WalletController::class, 'updateBalance'])->name('wallets.update');
        Route::get('/wallets/{id}/history', [WalletController::class, 'history'])->name('wallets.history');
    });
});
