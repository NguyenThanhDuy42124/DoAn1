<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});
// cụm của đăng ký, đăng nhập, đăng xuất
Route::post('/register', [UserController::class, 'register']); // gọi đến controller
Route::post('/logout', [UserController::class, 'logout']);
Route::post('/login', [UserController::class, 'login']);
// hết cụm của đăng ký, đăng nhập, đăng xuất

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');


Route::get('/switch-role/{role}', [UserController::class, 'switchRole'])->name('switchRole');

// Route riêng cho admin
Route::middleware('role:admin')->group(function () {
    Route::get('/admin/dashboard', [UserController::class, 'index'])->name('admin.dashboard');
});

// Route riêng cho seller
Route::middleware('role:seller')->group(function () {
    Route::get('/seller/dashboard', [UserController::class, 'index'])->name('seller.dashboard');
});
