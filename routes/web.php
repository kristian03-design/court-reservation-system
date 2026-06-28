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
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::redirect('/homepage', '/');
Route::view('/about', 'guest.guest-about')->name('about');
Route::view('/contact', 'guest.guest-contact')->name('contact');
Route::view('/privacy-policy', 'guest.privacy-policy')->name('privacy');
Route::view('/terms-of-service', 'guest.terms-of-service')->name('terms');
Route::get('/courts', [CourtController::class, 'index'])->name('courts.index')->middleware('throttle:api.search');
Route::get('/availability', [CourtController::class, 'globalAvailability'])->name('availability')->middleware('throttle:api.search');
Route::view('/events', 'guest.guest-events')->name('events');
Route::get('/tournaments', [App\Http\Controllers\TournamentController::class, 'index'])->name('tournaments');
Route::get('/tournaments/{tournament}', [App\Http\Controllers\TournamentController::class, 'show'])->name('tournaments.show');
Route::get('/tournaments-list', [App\Http\Controllers\TournamentController::class, 'index'])->name('tournaments.index');
Route::get('/courts/{court}', [CourtController::class, 'show'])->name('courts.show');
Route::get('/courts/{court}/availability', [CourtController::class, 'availability'])->name('courts.availability')->middleware('throttle:api.search');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:api.login');
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:api.register');
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('guest:admin')->group(function () {
    Route::get('/admin/login', [AuthenticatedSessionController::class, 'createAdmin'])->name('admin.login');
    Route::post('/admin/login', [AuthenticatedSessionController::class, 'storeAdmin'])->name('admin.login.store')->middleware('throttle:api.login');
    Route::get('/admin/otp', [AuthenticatedSessionController::class, 'createAdminOtp'])->name('admin.otp');
    Route::post('/admin/otp', [AuthenticatedSessionController::class, 'verifyAdminOtp'])->name('admin.otp.verify')->middleware('throttle:api.login');
});

Route::middleware('auth:web')->group(function () {
    Route::delete('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/verify-email', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

Route::middleware(['auth:web', 'verified', 'role:customer'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/reservations', [BookingController::class, 'index'])->name('reservations.index');
    Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store')->middleware('throttle:api.reservation');
    Route::get('/reservations/{reservation}', [BookingController::class, 'show'])->name('reservations.show');
    Route::patch('/reservations/{reservation}/cancel', [BookingController::class, 'cancel'])->name('reservations.cancel');
    Route::post('/payments/{payment}/proof', [PaymentController::class, 'store'])->name('payments.proof')->middleware('throttle:api.payment');
    Route::get('/payments/{payment}/proof', [PaymentController::class, 'showProof'])->name('payments.proof.show');
    Route::post('/payments/{payment}/pay-card', [PaymentController::class, 'payCard'])->name('payments.pay-card')->middleware('throttle:api.payment');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Notifications & Billing
    Route::get('/notifications', [DashboardController::class, 'notifications'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [DashboardController::class, 'markNotificationRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [DashboardController::class, 'markAllNotificationsRead'])->name('notifications.read-all');
    Route::get('/billing', [DashboardController::class, 'billing'])->name('payments.index');
    Route::get('/dashboard/tournaments/{tournament}', [DashboardController::class, 'tournamentShow'])->name('dashboard.tournaments.show');
});

Route::redirect('/admin/dashboard', '/admin')
    ->middleware(['auth:admin', 'role:staff'])
    ->name('admin.dashboard.redirect');

Route::prefix('admin')
    ->as('admin.')
    ->middleware(['auth:admin', 'role:staff', 'throttle:api.admin'])
    ->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');
        Route::delete('/logout', [AuthenticatedSessionController::class, 'destroyAdmin'])->name('logout');
        Route::resource('courts', Admin\CourtController::class)->except(['show']);
        Route::resource('courts.schedules', Admin\CourtScheduleController::class)->only(['index', 'store', 'destroy']);
        Route::resource('reservations', Admin\ReservationController::class)->only(['index', 'show', 'update', 'destroy']);
        Route::get('/reservation-calendar', [Admin\ReservationController::class, 'calendar'])->name('reservations.calendar');
        Route::resource('users', Admin\UserController::class)->only(['index', 'update', 'destroy'])->middleware('role:admin');
        Route::resource('payments', Admin\PaymentController::class)->only(['index', 'update', 'show'])->middleware('role:admin');
        Route::get('/payments/{payment}/proof', [Admin\PaymentController::class, 'showProof'])->name('payments.proof.show')->middleware('role:admin');
        Route::get('/reports', Admin\ReportController::class)->name('reports.index')->middleware('role:admin');
        Route::get('/settings', [Admin\SettingController::class, 'index'])->name('settings.index')->middleware('role:admin');
        Route::post('/settings', [Admin\SettingController::class, 'store'])->name('settings.store')->middleware('role:admin');

        Route::resource('tournaments', Admin\TournamentController::class);
        Route::post('/tournaments/{tournament}/publish', [Admin\TournamentController::class, 'publish'])->name('tournaments.publish');
        Route::post('/tournaments/{tournament}/generate', [Admin\TournamentController::class, 'generateBracket'])->name('tournaments.generate');
        Route::post('/tournaments/{tournament}/schedule', [Admin\TournamentController::class, 'scheduleMatches'])->name('tournaments.schedule');
        Route::post('/matches/{match}/score', [Admin\TournamentController::class, 'updateScore'])->name('matches.update-score');
        Route::post('/participants/{participant}/check-in', [Admin\TournamentController::class, 'checkIn'])->name('participants.check-in');

        // Admin notifications
        Route::post('/notifications/{notification}/read', [Admin\NotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [Admin\NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    });
