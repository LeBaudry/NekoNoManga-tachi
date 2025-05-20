<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JikanController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\AnimeController;
use App\Http\Controllers\EpisodeController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // Librairie personnelle
    Route::prefix('library')->group(function () {
        Route::get('animes', [LibraryController::class, 'index']);
        Route::post('animes', [LibraryController::class, 'store']);
        Route::delete('animes/{anime}', [LibraryController::class, 'destroy']);
    });

    // Animes & progression
    Route::get('animes/jikan/{malId}', [AnimeController::class, 'showByMalId']);
    Route::get('animes/{anime}/progression', [AnimeController::class, 'progression']);
    Route::post('animes/{anime}/episodes/{numero}/toggle', [EpisodeController::class, 'toggleVu']);
    Route::apiResource('animes', AnimeController::class)->except(['show']);
});

// Recherches publiques via Jikan
Route::prefix('jikan')->group(function () {
    Route::get('search', [JikanController::class, 'search']);
    Route::get('anime/{malId}/episodes', [JikanController::class, 'episodes']);
    Route::get('animes', [JikanController::class, 'list']);
});
