<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ArenaController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\OwnerRequestController;
use App\Http\Controllers\OwnerBookingController;
use Illuminate\Support\Facades\Route;

// Trang chủ
Route::get('/', function () {
    $arenas = \App\Models\Arena::whereIn('status', ['active', 'maintenance'])->take(6)->get();
    return view('welcome', compact('arenas'));
})->name('home');

// Bản đồ và Sân bãi
Route::get('/ban-do', [MapController::class, 'index'])->name('map');
Route::get('/san-bong', [ArenaController::class, 'publicIndex'])->name('arenas.index');
Route::get('/lich-trong', [ArenaController::class, 'availableIndex'])->name('arenas.available');
Route::get('/api/arenas', [MapController::class, 'getArenas']);

// Chatbot API
Route::post('/api/chatbot', [\App\Http\Controllers\ChatbotController::class, 'handle'])->name('chatbot.handle');

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
    Route::get('/hoa-don-dat-san', [BookingController::class, 'bill'])->name('bookings.bill');
    Route::get('/thanh-toan/chuyen-khoan', [BookingController::class, 'paymentTransfer'])->name('bookings.payment-transfer');
    Route::patch('/thanh-toan/xac-nhan', [BookingController::class, 'confirmPayment'])->name('bookings.confirm-payment');
    Route::get('/lich-su-dat-san', [BookingController::class, 'myBookings'])->name('bookings.my-bookings');
    Route::delete('/dat-san/{booking}/huy', [BookingController::class, 'cancel'])->name('bookings.user-cancel');
    Route::get('/api/booked-slots/{arena}', [BookingController::class, 'getBookedSlots'])->name('bookings.booked-slots');

    // Profile + Owner request
    Route::get('/profile', [OwnerRequestController::class, 'profile'])->name('profile');
    Route::post('/become-owner', [OwnerRequestController::class, 'requestOwner'])->name('owner-requests.store');
    Route::patch('/profile/password', [OwnerRequestController::class, 'changePassword'])->name('profile.password');

    // Test debug route
    Route::get('/test-auth', function () {
        $user = \Illuminate\Support\Facades\Auth::user();
        return response()->json([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'role' => $user->role ? $user->role->name : null,
            'role_id' => $user->role_id,
            'arenas_count' => $user->arenas()->count(),
            'arenas' => $user->arenas()->pluck('id')->toArray(),
        ]);
    })->name('test-auth');

    // Quản lý sân (Chỉ dành cho Chủ sân)
    Route::middleware(['role:owner'])->prefix('owner')->name('owner.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\OwnerDashboardController::class, 'index'])->name('dashboard');
        Route::resource('arenas', \App\Http\Controllers\OwnerArenaController::class);
        Route::get('/bookings', [\App\Http\Controllers\OwnerBookingController::class, 'index'])->name('bookings.index');
        Route::patch('/bookings/{booking}/confirm', [\App\Http\Controllers\OwnerBookingController::class, 'confirm'])->name('bookings.confirm');
        Route::patch('/bookings/{booking}/cancel', [\App\Http\Controllers\OwnerBookingController::class, 'cancel'])->name('bookings.cancel');
        Route::patch('/bookings/{booking}/status', [\App\Http\Controllers\OwnerBookingController::class, 'updateStatus'])->name('bookings.update-status');
    });

    // Quản lý sân (Chỉ dành cho Admin)
        Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
            Route::resource('arenas', \App\Http\Controllers\ArenaController::class);
            
            // Quản lý đơn đặt sân
            Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
            Route::patch('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.update-status');

            // Quản lý yêu cầu trở thành chủ sân
            Route::get('/owner-requests', [AdminController::class, 'index'])->name('owner-requests.index');
            Route::patch('/owner-requests/{ownerRequest}/approve', [AdminController::class, 'approve'])->name('owner-requests.approve');
            Route::patch('/owner-requests/{ownerRequest}/reject', [AdminController::class, 'reject'])->name('owner-requests.reject');
        });
    });
