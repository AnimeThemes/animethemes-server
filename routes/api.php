<?php

use App\Http\Controllers\Api\AnimeController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\ArtistController;
use App\Http\Controllers\Api\Billing\BalanceController;
use App\Http\Controllers\Api\Billing\TransactionController;
use App\Http\Controllers\Api\EntryController;
use App\Http\Controllers\Api\ExternalResourceController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SeriesController;
use App\Http\Controllers\Api\SongController;
use App\Http\Controllers\Api\SynonymController;
use App\Http\Controllers\Api\ThemeController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\YearController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['as' => 'api.'], function () {

    // Search Routes
    Route::get('search', [SearchController::class, 'index'])->name('search.index');

    // Billing Resources
    Route::apiResource('balance', BalanceController::class)->only(['index', 'show']);
    Route::apiResource('transaction', TransactionController::class)->only(['index', 'show']);

    // Resource Routes
    Route::apiResource('anime', AnimeController::class)->only(['index', 'show']);
    Route::apiResource('announcement', AnnouncementController::class)->only(['index', 'show']);
    Route::apiResource('artist', ArtistController::class)->only(['index', 'show']);
    Route::apiResource('entry', EntryController::class)->only(['index', 'show']);
    Route::apiResource('image', ImageController::class)->only(['index', 'show']);
    Route::apiResource('resource', ExternalResourceController::class)->only(['index', 'show']);
    Route::apiResource('series', SeriesController::class)->only(['index', 'show']);
    Route::apiResource('song', SongController::class)->only(['index', 'show']);
    Route::apiResource('synonym', SynonymController::class)->only(['index', 'show']);
    Route::apiResource('theme', ThemeController::class)->only(['index', 'show']);
    Route::apiResource('video', VideoController::class)->only(['index', 'show']);

    // Year Routes
    Route::get('year', [YearController::class, 'index'])->name('year.index');
    Route::get('year/{year}', [YearController::class, 'show'])->name('year.show');
});

Route::fallback(function () {
    abort(404);
});
