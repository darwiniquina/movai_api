<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TrendingController;
use App\Http\Controllers\TvShowController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes (Public)
|--------------------------------------------------------------------------
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');

    // Email verification routes
    Route::get('email/verify/{id}/{hash}', 'verifyEmail')
        ->middleware(['signed'])
        ->name('verification.verify');

    Route::post('email/verification-notification', 'resendVerification')
        ->middleware(['auth:sanctum', 'throttle:6,1'])
        ->name('verification.send');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (require authentication and email verification)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    // User routes
    Route::post('logout', [AuthController::class, 'logout']);
    Route::controller(UserController::class)->group(function () {
        Route::get('user', 'user');
    });

    // ==================== MOVIES ====================
    Route::prefix('movies')->group(function () {
        Route::get('popular', [MovieController::class, 'popular']);
        Route::get('top-rated', [MovieController::class, 'topRated']);
        Route::get('upcoming', [MovieController::class, 'upcoming']);
        Route::get('now-playing', [MovieController::class, 'nowPlaying']);
        Route::get('search', [MovieController::class, 'search']);
        Route::get('genre/{genreId}', [MovieController::class, 'discoverByGenre']);
        Route::get('{id}', [MovieController::class, 'show']);
        Route::get('{id}/similar', [MovieController::class, 'similar']);
        Route::get('{id}/recommendations', [MovieController::class, 'recommendations']);
    });

    // ==================== TV SHOWS ====================
    Route::prefix('tv')->group(function () {
        Route::get('popular', [TvShowController::class, 'popular']);
        Route::get('top-rated', [TvShowController::class, 'topRated']);
        Route::get('airing-today', [TvShowController::class, 'airingToday']);
        Route::get('on-the-air', [TvShowController::class, 'onTheAir']);
        Route::get('search', [TvShowController::class, 'search']);
        Route::get('genre/{genreId}', [TvShowController::class, 'discoverByGenre']);
        Route::get('{id}', [TvShowController::class, 'show']);
        Route::get('{id}/season/{seasonNumber}', [TvShowController::class, 'season']);
        Route::get('{id}/similar', [TvShowController::class, 'similar']);
    });

    // ==================== GENRES ====================
    Route::prefix('genres')->group(function () {
        Route::get('movies', [GenreController::class, 'movies']);
        Route::get('tv', [GenreController::class, 'tvShows']);
    });

    // ==================== SEARCH ====================
    Route::prefix('search')->group(function () {
        Route::get('multi', [SearchController::class, 'multi']);
        Route::get('people', [SearchController::class, 'people']);
    });

    // ==================== TRENDING ====================
    Route::get('trending', [TrendingController::class, 'index']);

    // ==================== PEOPLE ====================
    Route::get('person/{id}', [PersonController::class, 'show']);
});
