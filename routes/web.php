<?php

use App\Http\Controllers\Admin\BookingApprovalController;
use App\Http\Controllers\Admin\ResourceAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Api\ConflictController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResourceController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect(auth()->check() ? '/dashboard.php' : '/login.php'));
Route::get('/index.php', fn () => redirect(auth()->check() ? '/dashboard.php' : '/login.php'));

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login.php', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/login.php', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister']);
    Route::get('/register.php', [AuthController::class, 'showRegister']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/register.php', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/logout.php', [AuthController::class, 'logout']);

    Route::get('/dashboard', DashboardController::class);
    Route::get('/dashboard.php', DashboardController::class);

    Route::get('/resources', [ResourceController::class, 'index']);
    Route::get('/resources.php', [ResourceController::class, 'index']);

    Route::get('/booking/create', [BookingController::class, 'create']);
    Route::post('/booking/create', [BookingController::class, 'store']);
    Route::get('/booking_create.php', [BookingController::class, 'create']);
    Route::post('/booking_create.php', [BookingController::class, 'store']);

    Route::get('/api/events', EventController::class);
    Route::get('/api/events.php', EventController::class);
    Route::get('/api/check-conflict', ConflictController::class);
    Route::get('/api/check_conflict.php', ConflictController::class);

    Route::middleware('admin')->prefix('admin')->group(function (): void {
        Route::get('/bookings', [BookingApprovalController::class, 'index']);
        Route::post('/bookings', [BookingApprovalController::class, 'update']);
        Route::get('/bookings.php', [BookingApprovalController::class, 'index']);
        Route::post('/bookings.php', [BookingApprovalController::class, 'update']);

        Route::get('/resources', [ResourceAdminController::class, 'index']);
        Route::post('/resources', [ResourceAdminController::class, 'save']);
        Route::get('/resources.php', [ResourceAdminController::class, 'index']);
        Route::post('/resources.php', [ResourceAdminController::class, 'save']);

        Route::get('/users', [UserAdminController::class, 'index']);
        Route::post('/users', [UserAdminController::class, 'save']);
        Route::get('/users.php', [UserAdminController::class, 'index']);
        Route::post('/users.php', [UserAdminController::class, 'save']);
    });
});
