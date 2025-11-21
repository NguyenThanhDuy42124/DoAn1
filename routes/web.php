<?php

use App\Models\User;
use App\Livewire\MainPage;
use App\Livewire\TestBinding;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KycController;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\CartController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\UserInfoController;
use Illuminate\Auth\Middleware\Authenticate;
use App\Livewire\Seller\Products\ProductForm;
use App\Http\Controllers\StaticPageController;
use Illuminate\Session\Middleware\StartSession;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordResetController;
use App\Livewire\Seller\Stock\SellerTransactionHistory;
use App\Http\Controllers\ForgetPasswordController;
use App\Livewire\Admin\Brands\Manager as BrandManager;
use App\Livewire\Admin\Promotion\MainPagePromotionImage;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Livewire\Admin\Categories\Manager as CategoryManager;
use App\Livewire\Admin\Attributes\Manager as AttributeManager;
use App\Livewire\Seller\VoucherManager;

Route::get('/', MainPage::class)->name('main.page');

// Post là bắt dữ liệu gửi từ form
Route::post('/register', [UserController::class, 'register']); // gọi đến controller
Route::post('/logout', [UserController::class, 'logout']);
Route::post('/login', [UserController::class, 'login']);





// get là load trang web
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
});

Route::get('/dashboard', function () {
    return view('buyer.dashboard');
})->middleware('auth', 'check.status')->name('dashboard');

Route::get('/', function () {
    $path = null;
    if (Storage::disk('public')->exists('banner_path.txt')) {
        $path = Storage::disk('public')->get('banner_path.txt');
    }
    return view('mainpage', compact('path'));
});

//route google
Route::get('/auth/google/redirect', [App\Http\Controllers\GoogleAuthController::class, 'redirectToGoogle'])
    ->name('google.auth.redirect');

Route::get('/auth/google/call-back', [App\Http\Controllers\GoogleAuthController::class, 'handleGoogleCallback'])
    ->name('google.auth.callback');


// route của forget password
Route::post('/forgotPassword', [ForgetPasswordController::class, 'sendResetLink'])
    ->middleware('throttle:5,1')
    ->name('forgetPassword.link');

Route::get('/forgetPassword', [ForgetPasswordController::class, 'showForget_Password'])
    ->name('forgetPassword.form');

Route::post('/resetPassword', [PasswordResetController::class, 'resetPassword'])
    ->name('password.update');

Route::get('/resetPassword/{token}', [PasswordResetController::class, 'showReset_Password'])
    ->name('password.reset');

// Route để chuyển đổi vai trò giữa buyer sang admin và seller
Route::get('/switch-role/{role}', [UserController::class, 'switchRole'])->name('switchRole');

// Route riêng cho admin
Route::prefix('admin')->middleware('role:admin')->group(function () {
    //vừa truyền $users vừa gọi hàm dashboard trong AdminController
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/usersManager', [AdminController::class, 'userDashboard'])->name('admin.users.manager');
    Route::get('/kyc-image/{type}/{userId}', [KycController::class, 'show'])
    ->name('admin.kyc.image')
    ->middleware(['auth','role:admin']);

    // khai báo tài nguyên CRUD cho UserController
    Route::resource('users', AdminController::class);
    // 1. ĐẶT TRƯỚC resource
    Route::get('/products', function () {
        return view('admin.products.index');
    })->name('admin.products.index');


    Route::get('/categories', CategoryManager::class)->name('admin.categories.manager');
    Route::get('/attributes', AttributeManager::class)->name('admin.attributes.manager');
    Route::get('/brands', BrandManager::class)->name('admin.brands.manager');


    // Route cho quản lý ảnh trang chủ
    Route::get('/UserUIimages', MainPagePromotionImage::class)->name('admin.homepage.images.manager');

    // 2. Resource chỉ dùng cho CRUD chi tiết (create, edit, …)
    Route::resource('products', ProductController::class, ['names' => 'admin.products'])
        ->except(['index']);   // <-- loại bỏ GET /admin/products



    //route thong bao
    Route::get('/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::get('/notifications/create', [NotificationController::class, 'create'])->name('admin.notifications.create');
    Route::post('/notifications', [NotificationController::class, 'store'])->name('admin.notifications.store');
    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('admin.notifications.show');
    Route::get('/notifications/{notification}/edit', [NotificationController::class, 'edit'])->name('admin.notifications.edit');
    Route::put('/notifications/{notification}', [NotificationController::class, 'update'])->name('admin.notifications.update');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('admin.notifications.destroy');
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('admin.notifications.markAsRead');
    Route::post('/notifications/{notification}/mark-as-unread', [NotificationController::class, 'markAsUnread'])->name('admin.notifications.markAsUnread');
});



Route::get('/test-binding', TestBinding::class);


// Route riêng cho seller
Route::prefix('seller')->middleware('role:seller')->group(function () {
    // Sửa thành SellerController::dashboard

    Route::get('stock/history', SellerTransactionHistory::class)->name('seller.stock.history');

    Route::get('/dashboard', [SellerController::class, 'dashboard'])->name('seller.dashboard');
    // THAY THẾ 2 ROUTE CŨ BẰNG 2 ROUTE NÀY
    Route::get('/products/create', ProductForm::class)->name('seller.products.create');
    // Laravel tự động tìm product dựa trên ID {product} và truyền vào mount()
    Route::get('/products/{productId}/edit', ProductForm::class)->name('seller.products.edit');

    // Chỉ giữ lại index và destroy cho ProductController
    Route::resource('products', ProductController::class, ['names' => 'seller.products'])
        ->only(['index', 'destroy']);


    // [GET] Route để hiển thị trang form
    Route::get('/products/import/form', [ProductController::class, 'showImportForm'])->name('seller.products.import.form');

    // [POST] Route để xử lý dữ liệu từ form
    Route::post('/products/import', [ProductController::class, 'import'])->name('seller.products.import');




    Route::get('/vouchers', VoucherManager::class)->name('seller.vouchers.index');


    Route::get('/orders/{id}', [SellerController::class, 'show'])->name('seller.orders.show');
    Route::get('/orders', function () {
        return view('seller.orders.index');
    })->name('seller.orders.index');
    Route::get('/reports', [SellerController::class, 'showReportPage'])->name('seller.reports.index');
    Route::get('/seller/api/revenue-report', [SellerController::class, 'getRevenueReport'])
        ->name('seller.api.revenue.report');
});
Route::get('/shop/{id}', [SellerController::class, 'showShop'])->name('shop.show');
Route::get('/products', [ProductController::class, 'listProducts'])->name('products.list');
Route::get('/products/{id}', [ProductController::class, 'showProductDetail'])->name('products.detail');
Route::get('/vouchers', [VoucherController::class, 'listVouchers'])->name('vouchers.list');
Route::get('/', [SellerController::class, 'index'])->name('home');

Route::post('/webhook', [CheckoutController::class, 'webhook'])
    ->name('buyer.checkout.webhook')
    ->withoutMiddleware([
        'web',  // Skip entire 'web' group (session + CSRF + etc.)
        // Hoặc explicit classes nếu 'web' không work
        StartSession::class,
        VerifyCsrfToken::class,
        Authenticate::class,  // Nếu route inherit auth
    ]);

Route::prefix('info')->name('pages.')->group(function () {
    // Nhóm Hỗ Trợ Khách Hàng
    Route::get('/faq', [StaticPageController::class, 'faq'])->name('faq');
    Route::get('/warranty-policy', [StaticPageController::class, 'warrantyPolicy'])->name('warranty');
    Route::get('/return-policy', [StaticPageController::class, 'returnPolicy'])->name('return');
    // Trang tra cứu đơn hàng (sẽ làm riêng)
    Route::get('/order-tracking', [StaticPageController::class, 'orderTracking'])->name('order-tracking');

    // Nhóm Thông tin & Chính sách
    Route::get('/about-us', [StaticPageController::class, 'about'])->name('about');
    Route::get('/careers', [StaticPageController::class, 'careers'])->name('careers');
    Route::get('/privacy-policy', [StaticPageController::class, 'privacyPolicy'])->name('privacy');
    Route::get('/terms-of-service', [StaticPageController::class, 'termsOfService'])->name('terms');

    // Bạn có thể thêm các trang Kênh Người Bán ở đây sau
    // Route::get('/seller-support', [StaticPageController::class, 'sellerSupport'])->name('seller-support');
    // Route::get('/marketplace-rules', [StaticPageController::class, 'marketplaceRules'])->name('marketplace-rules');
});

Route::middleware('auth')->group(function () {
    Route::get('general/users/{id}/edit', [UserInfoController::class, 'edit'])->name('general.users.edit');
    Route::put('general/users/{id}', [UserInfoController::class, 'update'])->name('general.users.update');

    Route::get('/carts', function () {
        // Đảm bảo 'buyer.carts.index' trỏ đến file:
        // resources/views/buyer/carts/index.blade.php
        return view('buyer.carts.index');
    })->name('buyer.carts.index');

    Route::get('/buyer/dashboard', [UserController::class, 'requestToBecomeSeller'])
        ->name('buyer.RequestToBecomeSeller');


    Route::post('/carts', [CartController::class, 'store'])->name('buyer.carts.store');
    Route::post('/carts/add', [CartController::class, 'AddCart'])->name('buyer.carts.add');
    Route::get('/cart-items/{id}/edit', [CartItemController::class, 'edit'])->name('buyer.cart_items.edit');
    Route::put('/cart-items/{id}', [CartItemController::class, 'update'])->name('buyer.cart_items.update');
    Route::delete('/cart/item/{id}', [CartItemController::class, 'destroy'])->name('buyer.cart_items.destroy');

    Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('buyer.checkouts.checkout');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('buyer.checkouts.success');
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('buyer.checkouts.cancel');

    Route::get('/checkouts/purchase-history', [OrderController::class, 'purchaseHistory'])->name('buyer.checkouts.purchase_history');

    Route::get('/buyer/orders', [BuyerController::class, 'orders'])->name('buyer.orders.index');
    Route::patch('/buyer/orders/{id}/cancel', [BuyerController::class, 'cancelOrder'])->name('buyer.orders.cancel');
    Route::post('/buyer/orders/{id}/confirm', [BuyerController::class, 'confirmOrder'])->name('buyer.orders.confirm');
    Route::post('buyer/orders/{id}/return', [BuyerController::class, 'returnOrder'])->name('buyer.orders.return');
    Route::post('/orders/{order}/repurchase', [App\Http\Controllers\OrderController::class, 'repurchase'])->name('orders.repurchase');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/{notification}/mark-as-unread', [NotificationController::class, 'markAsUnread'])->name('notifications.markAsUnread');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    Route::post('follow/{seller}', [FollowController::class, 'toggleFollow'])->name('seller.follow.toggle');

    Route::get('/checkout/review', [CheckoutController::class, 'review'])->name('buyer.checkouts.review');
    Route::post('/checkout/voucher/apply', [CheckoutController::class, 'applyVoucher'])->name('buyer.checkouts.apply-voucher');
    Route::post('/checkout/voucher/remove', [CheckoutController::class, 'removeVoucher'])->name('buyer.checkouts.remove-voucher');
    Route::post('/checkout/process', [CheckoutController::class, 'processPayment'])->name('buyer.checkouts.process');

});
Route::post('/buyer/request-seller', [\App\Http\Controllers\UserController::class, 'requestToBecomeSeller'])
    ->name('buyer.requestToBecomeSeller')
    ->middleware('auth');
