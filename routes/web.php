<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('MainPage');
});
// cụm của đăng ký, đăng nhập, đăng xuất
Route::post('/register', [UserController::class, 'register']); // gọi đến controller
Route::post('/logout', [UserController::class, 'logout']);
Route::post('/login', [UserController::class, 'login']);




// hết cụm của đăng ký, đăng nhập, đăng xuất
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::get('/register', function () {
    return view('auth.register');
});
Route::get('/dashboard', function () {
    return view('buyer.dashboard');
})->middleware('auth')->name('dashboard');



Route::get('/switch-role/{role}', [UserController::class, 'switchRole'])->name('switchRole');

// Route riêng cho admin
Route::prefix('admin')->middleware('role:admin')->group(function () {
    //vừa truyền $users vừa gọi hàm dashboard trong UserController
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('admin.dashboard');
    Route::resource('users', UserController::class, ['name' => 'admin']);

});


// Route riêng cho seller
Route::middleware('role:seller')->group(function () {
    Route::get('/seller/dashboard', [UserController::class, 'index'])->name('seller.dashboard');
});
