<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Admin\AnnouncementController;
use App\Http\Controllers\Api\Billing\BalanceController;
use App\Http\Controllers\Api\Billing\TransactionController;
use App\Http\Controllers\Api\Wiki\AnimeController;
use App\Http\Controllers\Api\Wiki\ArtistController;
use App\Http\Controllers\Api\Wiki\EntryController;
use App\Http\Controllers\Api\Wiki\ExternalResourceController;
use App\Http\Controllers\Api\Wiki\ImageController;
use App\Http\Controllers\Api\Wiki\SearchController;
use App\Http\Controllers\Api\Wiki\SeriesController;
use App\Http\Controllers\Api\Wiki\SongController;
use App\Http\Controllers\Api\Wiki\SynonymController;
use App\Http\Controllers\Api\Wiki\ThemeController;
use App\Http\Controllers\Api\Wiki\VideoController;
use App\Http\Controllers\Api\Wiki\YearController;
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
    Route::get('search', [SearchController::class, 'show'])->name('search.show');

    // Admin Resources
    Route::apiResource('announcement', AnnouncementController::class)->only(['index', 'show']);

    // Billing Resources
    Route::apiResource('balance', BalanceController::class)->only(['index', 'show']);
    Route::apiResource('transaction', TransactionController::class)->only(['index', 'show']);

    // Wiki Resources
    Route::apiResource('anime', AnimeController::class)->only(['index', 'show']);
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
