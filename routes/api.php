<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\MediaItemController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TrendingController;
use App\Http\Controllers\TvShowController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WatchlistItemController;
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

    Route::post('logout', [AuthController::class, 'logout']);
    Route::controller(UserController::class)->group(function () {
        Route::get('user', 'user');
    });

    Route::prefix('movies')->controller(MovieController::class)->group(function () {
        Route::get('popular', 'popular');
        Route::get('top-rated', 'topRated');
        Route::get('upcoming', 'upcoming');
        Route::get('now-playing', 'nowPlaying');
        Route::get('search', 'search');
        Route::get('genre/{genreId}', 'discoverByGenre');

        Route::get('{id}', 'show');
        Route::get('{id}/similar', 'similar');
        Route::get('{id}/recommendations', 'recommendations');
    });

    Route::prefix('tv')->controller(TvShowController::class)->group(function () {
        Route::get('popular', 'popular');
        Route::get('top-rated', 'topRated');
        Route::get('airing-today', 'airingToday');
        Route::get('on-the-air', 'onTheAir');
        Route::get('search', 'search');
        Route::get('genre/{genreId}', 'discoverByGenre');

        Route::get('{id}', 'show');
        Route::get('{id}/season/{seasonNumber}', 'season');
        Route::get('{id}/similar', 'similar');
    });

    Route::prefix('genres')->controller(GenreController::class)->group(function () {
        Route::get('movies', 'movies');
        Route::get('tv', 'tvShows');
    });

    Route::prefix('search')->controller(SearchController::class)->group(function () {
        Route::get('multi', 'multi');
        Route::get('people', 'people');
    });

    Route::get('trending', [TrendingController::class, 'index']);

    Route::get('person/{id}', [PersonController::class, 'show']);

    /* Not TMDB Related */

    Route::prefix('media')->controller(MediaItemController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('{mediaItem}', 'show');
    });

    Route::prefix('watchlist')->controller(WatchlistItemController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::delete('/{watchlistItem}', 'destroy');
    });

    Route::prefix('favorites')->controller(FavoriteController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::delete('/{favorite}', 'destroy');
    });

    Route::prefix('reviews')->controller(ReviewController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{review}', 'show');
        Route::delete('/{review}', 'destroy');
    });

    Route::prefix('users')->controller(UserController::class)->group(function () {
        Route::get('search', 'search');
        Route::get('{user}', 'show');
    });
});
