<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ApiBookingController;
use App\Http\Controllers\Api\V1\ApiCourtController;
use App\Http\Controllers\Api\V1\AuthController;

// ──────────────────────────────────────────────────────
// API v1 — all require a valid request signature header
// ──────────────────────────────────────────────────────
Route::prefix('v1')->middleware('signature')->group(function () {

    // ── Auth endpoints (public) ───────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('/login',    [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/refresh',  [AuthController::class, 'refresh']); // uses refresh token in Bearer
    });

    // ── Auth endpoints (requires valid access JWT) ────
    Route::middleware('jwt.auth')->prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);
    });

    // ── Public court endpoints ────────────────────────
    Route::middleware('throttle:api.search')->group(function () {
        Route::get('/courts',                        [ApiCourtController::class, 'index']);
        Route::get('/courts/{court}/availability',   [ApiCourtController::class, 'availability']);
    });

    // ── Protected reservation endpoints ──────────────
    Route::middleware(['jwt.auth', 'throttle:api.reservation'])->group(function () {
        Route::get('/reservations',               [ApiBookingController::class, 'index']);
        Route::post('/reservations',              [ApiBookingController::class, 'store']);
        Route::get('/reservations/{reservation}', [ApiBookingController::class, 'show']);
    });
});

// ── Tournament public API (no signature required) ────
Route::get('/tournaments/{id}/participants',  [App\Http\Controllers\Api\TournamentApiController::class, 'participants']);
Route::get('/tournaments/{id}/matches-data', [App\Http\Controllers\Api\TournamentApiController::class, 'matchesData']);
