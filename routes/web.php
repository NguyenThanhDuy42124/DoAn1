<?php


use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NotificationController;

Route::resource('products', ProductController::class);

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
    Route::get('/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('/notifications', [NotificationController::class, 'store'])->name('admin.notifications.store');

});


// Route riêng cho seller
Route::prefix('seller')->middleware('role:seller')->group(function () {
    Route::get('/dashboard', [UserController::class, 'index'])->name('seller.dashboard');
});
