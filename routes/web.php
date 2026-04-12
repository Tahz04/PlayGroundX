<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Trang chủ
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Auth routes (chỉ dành cho khách chưa đăng nhập)
Route::middleware('guest')->group(function () {
    Route::get('/dang-ky',     [AuthController::class, 'showRegister'])->name('register');
    Route::post('/dang-ky',    [AuthController::class, 'register']);

    Route::get('/dang-nhap',   [AuthController::class, 'showLogin'])->name('login');
    Route::post('/dang-nhap',  [AuthController::class, 'login']);
});

// Route đăng xuất (chỉ cho user đã đăng nhập)
Route::middleware('auth')->group(function () {
    Route::post('/dang-xuat',  [AuthController::class, 'logout'])->name('logout');
});
