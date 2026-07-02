<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ApiBookingController;
use App\Http\Controllers\Api\V1\ApiCourtController;

Route::prefix('v1')->middleware('signature')->group(function () {
    Route::middleware('throttle:api.search')->get('/courts', [ApiCourtController::class, 'index']);
    Route::middleware('throttle:api.search')->get('/courts/{court}/availability', [ApiCourtController::class, 'availability']);

    Route::middleware(['auth:sanctum', 'throttle:api.reservation'])->group(function () {
        Route::get('/reservations', [ApiBookingController::class, 'index']);
        Route::post('/reservations', [ApiBookingController::class, 'store']);
        Route::get('/reservations/{reservation}', [ApiBookingController::class, 'show']);
    });
});

Route::get('/tournaments/{id}/participants', [App\Http\Controllers\Api\TournamentApiController::class, 'participants']);
Route::get('/tournaments/{id}/matches-data', [App\Http\Controllers\Api\TournamentApiController::class, 'matchesData']);
