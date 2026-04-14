<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ArenaController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

// Trang chủ
Route::get('/', function () {
    $arenas = \App\Models\Arena::where('status', true)->take(6)->get();
    return view('welcome', compact('arenas'));
})->name('home');

// Bản đồ và Sân bãi
Route::get('/ban-do', [MapController::class, 'index'])->name('map');
Route::get('/san-bong', [ArenaController::class, 'publicIndex'])->name('arenas.index');
Route::get('/api/arenas', [MapController::class, 'getArenas']);

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

    // Booking
    Route::get('/dat-san/{arena}', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/dat-san', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/lich-su-dat-san', [BookingController::class, 'myBookings'])->name('bookings.my-bookings');

    // Quản lý sân (Chỉ dành cho Admin)
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('arenas', \App\Http\Controllers\ArenaController::class);
        
        // Quản lý đơn đặt sân
        Route::get('/bookings', [\App\Http\Controllers\AdminBookingController::class, 'index'])->name('bookings.index');
        Route::patch('/bookings/{booking}/status', [\App\Http\Controllers\AdminBookingController::class, 'updateStatus'])->name('bookings.update-status');
    });
});
