<?php


use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ForgetPasswordController;
use App\Http\Controllers\NotificationController;



Route::get('/', function () {
    return view('MainPage');
});
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
})->middleware('auth')->name('dashboard');


// route của forget password
Route::post('/forgotPassword', [ForgetPasswordController::class, 'sendResetLink'])->middleware('throttle:5,1') ->name('forgetPassword.link');
Route::get('/forgetPassword', [ForgetPasswordController::class,'showForget_Password'])->name('forgetPassword.form');

Route::post('/resetPassword', [PasswordResetController::class, 'resetPassword'])->name('password.update');
Route::get('/resetPassword/{token}', [PasswordResetController::class, 'showReset_Password'])->name('password.reset');


// Route để chuyển đổi vai trò giữa buyer sang admin và seller
Route::get('/switch-role/{role}', [UserController::class, 'switchRole'])->name('switchRole');

// Route riêng cho admin
Route::prefix('admin')->middleware('role:admin')->group(function () {
    //vừa truyền $users vừa gọi hàm dashboard trong AdminController
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/usersManager', [AdminController::class, 'userDashboard'])->name('admin.users.manager');
    // khai báo tài nguyên CRUD cho UserController
    Route::resource('users', AdminController::class, ['name' => 'admin']);
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::resource('products', ProductController::class, ['name' => 'admin.products']);
    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::resource('categories', CategoryController::class, ['names' => 'admin.categories']);
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


// Route riêng cho seller
Route::prefix('seller')->middleware('role:seller')->group(function () {
    Route::get('/dashboard', [UserController::class, 'index'])->name('seller.dashboard');
    Route::resource('products', ProductController::class, ['names' => 'seller.products']);
});
