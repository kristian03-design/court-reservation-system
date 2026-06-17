<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::view('/about', 'pages.about')->name('about');
Route::view('/contact', 'pages.contact')->name('contact');
Route::get('/courts', [CourtController::class, 'index'])->name('courts.index');
Route::get('/courts/{court}', [CourtController::class, 'show'])->name('courts.show');
Route::get('/courts/{court}/availability', [CourtController::class, 'availability'])->name('courts.availability');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/admin/login', [AuthenticatedSessionController::class, 'createAdmin'])->name('admin.login');
    Route::post('/admin/login', [AuthenticatedSessionController::class, 'storeAdmin'])->name('admin.login.store');
    Route::get('/admin/otp', [AuthenticatedSessionController::class, 'createAdminOtp'])->name('admin.otp');
    Route::post('/admin/otp', [AuthenticatedSessionController::class, 'verifyAdminOtp'])->name('admin.otp.verify');
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::delete('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/verify-email', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/reservations/{reservation}', [BookingController::class, 'show'])->name('reservations.show');
    Route::patch('/reservations/{reservation}/cancel', [BookingController::class, 'cancel'])->name('reservations.cancel');
    Route::post('/payments/{payment}/proof', [PaymentController::class, 'store'])->name('payments.proof');
});

Route::redirect('/admin/dashboard', '/admin')
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.dashboard.redirect');

Route::prefix('admin')
    ->as('admin.')
    ->middleware(['auth', 'verified', 'role:admin'])
    ->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');
        Route::resource('courts', Admin\CourtController::class)->except(['show']);
        Route::resource('reservations', Admin\ReservationController::class)->only(['index', 'show', 'update', 'destroy']);
        Route::get('/reservation-calendar', [Admin\ReservationController::class, 'calendar'])->name('reservations.calendar');
        Route::resource('users', Admin\UserController::class)->only(['index', 'update', 'destroy']);
        Route::resource('payments', Admin\PaymentController::class)->only(['index', 'update']);
        Route::get('/reports', Admin\ReportController::class)->name('reports.index');
        Route::get('/settings', [Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [Admin\SettingController::class, 'store'])->name('settings.store');
    });
