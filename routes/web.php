<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Import Client Controllers
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ProductController as ClientProductController;

// Import Admin & Auth Controllers
use App\Http\Controllers\AdminControllers\CategoryController;
use App\Http\Controllers\AdminControllers\CategoryFilterController;
use App\Http\Controllers\AdminControllers\BrandController;
use App\Http\Controllers\AdminControllers\ProductController as AdminProductController;
use App\Http\Controllers\AdminControllers\AttributeController;
use App\Http\Controllers\AdminControllers\AttributeValueController;
use App\Http\Controllers\AuthControllers\AuthController;
use App\Http\Controllers\AdminControllers\VoucherController;
use App\Http\Controllers\AdminControllers\UserController;
use App\Http\Controllers\AdminControllers\OrderController;
use App\Http\Controllers\AdminControllers\PointController;

use App\Http\Controllers\AdminControllers\PostController;
use App\Http\Controllers\AdminControllers\PostCategoryController;
use App\Http\Controllers\AdminControllers\WalletController;

use App\Http\Controllers\Client\PaymentController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\WalletController as ClientWalletController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\OrderController as ClientOrderController;
use App\Http\Controllers\Client\ChatbotController;
use App\Http\Controllers\Client\PostController as ClientPostController;
use App\Http\Controllers\Client\VoucherController as ClientVoucherController;
use App\Models\User;
use App\Http\Controllers\AdminControllers\CommentController as AdminCommentController;
use App\Http\Controllers\CommentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==========================================
// HỆ THỐNG CLIENT (Public)
// ==========================================

Route::middleware('check.verified')->group(function () {
    // Trang chủ
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Chi tiết sản phẩm & Danh sách sản phẩm
    Route::get('/san-pham/{slug}', [ClientProductController::class, 'show'])->name('client.product.detail');
    Route::get('/san-pham', [ClientProductController::class, 'index'])->name('client.products.index');

    // Thông tin tài khoản
    Route::get('profile/wallet', [ProfileController::class, 'user_wallet'])->name('profile.wallet');
    Route::resource('profile', ProfileController::class);
    Route::post('prodile/password/update/{id}',[ProfileController::class,'passwordUpdate'])->name('profile.password.update');

    // Nạp ví (ĐÃ FIX CHUẨN XỊN)
    Route::post('/wallet/deposit',[ClientWalletController::class,'createDeposit'])->name('wallet.deposit');
    Route::get('/wallet/vnpay-return', [ClientWalletController::class, 'vnpayReturn'])->name('wallet.vnpay.return');

    // Rút ví
    Route::post('/wallet/withdrawal', [ClientWalletController::class, 'withdrawalPost'])->name('wallet.withdrawal');
    Route::post('/wallet/withdrawal/cancelled/{id}', [ClientWalletController::class, 'withdrawalCancelled'])->name('wallet.withdrawal.cancelled');

    // QUẢN LÝ GIỎ HÀNG
    Route::post('/cart/add', [CartController::class, 'add'])->name('client.cart.add');
    Route::get('/cart/count', [CartController::class, 'count'])->name('client.cart.count');
    Route::get('/gio-hang', [CartController::class, 'index'])->name('client.cart.index');
    Route::post('/cart/update', [CartController::class, 'update'])->name('client.cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('client.cart.remove');
    Route::post('/cart/apply-voucher', [CartController::class, 'applyVoucher'])->name('client.cart.apply_voucher');

    // THANH TOÁN (CHECKOUT)
    Route::get('/thanh-toan', [CheckoutController::class, 'index'])->name('client.checkout.index');
    Route::post('/thanh-toan', [CheckoutController::class, 'process'])->name('client.checkout.process');
    Route::get('/dat-hang-thanh-cong', [CheckoutController::class, 'success'])->name('client.checkout.success');
    Route::get('/vnpay/response', [App\Http\Controllers\Client\CheckoutController::class, 'vnpay_return'])->name('vnpay.return');

    // QUẢN LÝ ĐƠN HÀNG CỦA KHÁCH
    Route::middleware(['auth'])->group(function () {
        Route::get('/don-mua', [ClientOrderController::class, 'index'])->name('client.orders.index');
        Route::get('/don-mua/{id}', [ClientOrderController::class, 'show'])->name('client.orders.show');
        Route::patch('/don-mua/{id}/xac-nhan', [ClientOrderController::class, 'confirmReceived'])->name('client.orders.confirm');
        Route::patch('/don-mua/{id}/huy', [ClientOrderController::class, 'cancel'])->name('client.orders.cancel');
    });

    Route::get('/bai-viet', [ClientPostController::class, 'index'])->name('client.posts.index');
    Route::get('/bai-viet/{slug}', [ClientPostController::class, 'show'])->name('client.posts.show');
    Route::post('/chatbot/chat', [ChatbotController::class, 'chat'])->name('chatbot.chat');

    // QUẢN LÝ ĐIỂM THƯỞNG (BEE POINT)
    Route::get('/bee-point', [App\Http\Controllers\Client\PointController::class, 'index'])->name('client.points.index');
    Route::post('/bee-point/redeem', [App\Http\Controllers\Client\PointController::class, 'redeem'])->name('client.points.redeem');

    // Voucher người dùng
    Route::get('user/vouchers' ,[ProfileController::class,'user_voucher'])->name('user.vouchers');
    Route::delete('user/vouchers/{id}', [ClientVoucherController::class, 'delete'])->name('user.vouchers.delete');

    // Danh sách vouchers
    Route::get('vouchers' ,[ClientVoucherController::class,'index'])->name('vouchers');

    // Lưu voucher người dùng
    Route::post('vouchers/save/{id}', [ClientVoucherController::class, 'saveVoucher'])->name('vouchers.save');
});

// ==========================================
// ĐĂNG NHẬP / ĐĂNG KÝ / QUÊN MẬT KHẨU
// ==========================================
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

// ==========================================
// XÁC THỰC EMAIL
// ==========================================
// Kiểm tra xác thực email chưa
Route::get('/email/verify', function () {
    $user = User::findOrFail(Auth::id());
    if ($user && $user->hasVerifiedEmail()) {
        return redirect()->route('home')->with([
            'success' => 'Đăng nhập thành công'
        ]);
    }
    return view('auth.verify_email.index');
})->name('verification.notice');

// Xác thực email
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('home')->with([
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

// ĐÃ FIX: Chỉ dùng quyền admin/staff ở ngoài cùng, quyền order.view để riêng vào nhóm đơn hàng

Route::middleware(['auth', 'verified', 'role:admin,staff'])->group(function () {

    Route::prefix('admin')->name('admin.')->group(function () {

        // Bảng điều khiển
        Route::get('/', function () {
            return view('admin.dashboard.index');
        })->name('dashboard');

        // Quản lý Users
        Route::resource('users', UserController::class);
        Route::post('user/{id}/block', [UserController::class, 'block'])->name('user.block');
        Route::post('user/{id}/unblock', [UserController::class, 'unBlock'])->name('user.unblock');
        Route::post('user/{id}/reset', [UserController::class, 'resetPw'])->name('resetPw');

        // 1. Quản lý Danh mục & Thương hiệu
        Route::get('categories/trash', [CategoryController::class, 'trash'])->name('categories.trash');
        Route::post('categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
        Route::delete('categories/{id}/force-delete', [CategoryController::class, 'forceDelete'])->name('categories.force_delete');
        Route::resource('categories', CategoryController::class)->except(['show']);

        Route::get('brands/trash', [BrandController::class, 'trash'])->name('brands.trash');
        Route::post('brands/{id}/restore', [BrandController::class, 'restore'])->name('brands.restore');
        Route::delete('brands/{id}/force-delete', [BrandController::class, 'forceDelete'])->name('brands.force_delete');
        Route::resource('brands', BrandController::class)->except(['show']);

        // 2. Quản lý Bộ lọc theo Danh mục
        Route::get('categories/{category}/filters', [CategoryFilterController::class, 'edit'])->name('categories.filters.edit');
        Route::put('categories/{category}/filters', [CategoryFilterController::class, 'update'])->name('categories.filters.update');
        Route::post('categories/{category}/filters/attributes', [CategoryFilterController::class, 'storeAttribute'])->name('categories.filters.attributes.store');
        Route::patch('categories/{category}/filters/attributes/{attribute}/toggle', [CategoryFilterController::class, 'toggleFilterable'])->name('categories.filters.attributes.toggle');
        Route::delete('categories/{category}/filters/attributes/{attribute}', [CategoryFilterController::class, 'detachAttribute'])->name('categories.filters.attributes.detach');


        // 3. Quản lý Thuộc tính gốc (Attributes)
        Route::get('attributes/trash', [AttributeController::class, 'trash'])->name('attributes.trash');
        Route::post('attributes/{id}/restore', [AttributeController::class, 'restore'])->name('attributes.restore');
        Route::delete('attributes/{id}/force-delete', [AttributeController::class, 'forceDelete'])->name('attributes.force_delete');
        Route::resource('attributes', AttributeController::class)->except(['create', 'show', 'edit']);
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
        Route::get('products/trash', [AdminProductController::class, 'trash'])->name('products.trash');
        Route::post('products/{id}/restore', [AdminProductController::class, 'restore'])->name('products.restore');
        Route::delete('products/{id}/force-delete', [AdminProductController::class, 'forceDelete'])->name('products.force_delete');
        Route::get('products/create', [AdminProductController::class, 'create'])->name('products.create');
        Route::post('products', [AdminProductController::class, 'store'])->name('products.store');
        Route::resource('products', AdminProductController::class)->except(['create', 'store']);

        // 5.1 Quản lý Comments (Admin)
        Route::get('comments', [AdminCommentController::class, 'index'])->name('comments.index');
        Route::post('comments/{comment}/reply', [AdminCommentController::class, 'reply'])->name('comments.reply');
        Route::delete('comments/{comment}', [AdminCommentController::class, 'destroy'])->name('comments.destroy');

        // Link từ danh sách sản phẩm -> trang quản lý comments (lọc theo product nếu cần)
        // Xem comment theo từng sản phẩm (UI giống trang com/showcom, khác với trang quản lý comment dạng bảng)
        Route::get('products/{product}/comments', [AdminProductController::class, 'comments'])->name('products.comments');


        // 6. Quản lý Vouchers
        Route::post('vouchers/{id}/restore', [VoucherController::class, 'restore'])->name('vouchers.restore');
        Route::resource('vouchers', VoucherController::class);

        // 7. Quản lý Đơn hàng (QUYỀN ORDER VIEW ĐƯỢC CHUYỂN VÀO ĐÂY)
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status.update');
        Route::patch('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::patch('orders/{order}/return-confirm', [OrderController::class, 'confirmReturn'])->name('orders.return.confirm');
        Route::get('orders/{order}/print-pdf', [OrderController::class, 'printPdf'])->name('orders.print.pdf');

        // 8. Quản lý bài viết
        Route::resource('posts', PostController::class);
        Route::resource('post-categories', PostCategoryController::class);
        Route::post('posts/upload', [PostController::class, 'upload'])->name('posts.upload');
        Route::post('posts/toggle-status/{id}', [PostController::class, 'toggleStatus'])
            ->name('posts.toggleStatus');

        // 9. Quản lý Ví tiền
        Route::get('/wallets', [WalletController::class, 'index'])->name('wallets.index');
        Route::post('/wallets/update', [WalletController::class, 'update'])->name('wallets.update');
        Route::get('/wallets/{id}/history', [WalletController::class, 'history'])->name('wallets.history');

        // Giao diện xem sao kê Ví Tổng
        Route::get('/system-wallet', [WalletController::class, 'systemWallet'])->name('system_wallet');

        // Nút xử lý cộng tiền cho User
        Route::post('/system-wallet/add-money', [WalletController::class, 'addMoneyToUser'])->name('system_wallet.add_money');

        // 10. Quản lý Điểm thưởng (Bee Point)
        Route::get('/points', [PointController::class, 'index'])->name('points.index');
        Route::post('/points/settings', [PointController::class, 'updateSettings'])->name('points.settings.update');
    });
});
// Public product routes and comment endpoints
Route::get('/products/{product}', [ClientProductController::class, 'show'])->name('products.show');
Route::post('/products/{product}/comments', [CommentController::class, 'store'])->name('products.comments.store');

