<?php

use App\Http\Controllers\AnimeController;
use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\JikanController;
use App\Http\Controllers\LibraryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::get('jikan/search', [JikanController::class, 'search']);
Route::get('jikan/anime/{malId}/episodes', [JikanController::class, 'episodes']);
Route::get('jikan/animes', [JikanController::class, 'list']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get  ('library/animes',            [LibraryController::class, 'index']);
    Route::post ('library/animes',            [LibraryController::class, 'store']);
    Route::delete('library/animes/{anime}',   [LibraryController::class, 'destroy']);

    Route::get('animes/jikan/{malId}', [AnimeController::class, 'showByMalId']);

    Route::apiResource('animes', AnimeController::class)
    ->except(['show']);
    Route::post('animes/{anime}/episodes/{episode}/toggle', [EpisodeController::class, 'toggleVu']);
    Route::get('animes/{anime}/progression', [AnimeController::class, 'progression']);
});
