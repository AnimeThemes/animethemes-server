<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Admin\AnnouncementController;
use App\Http\Controllers\Api\Billing\BalanceController;
use App\Http\Controllers\Api\Billing\TransactionController;
use App\Http\Controllers\Api\Config\FlagsController;
use App\Http\Controllers\Api\Config\WikiController;
use App\Http\Controllers\Api\Document\PageController;
use App\Http\Controllers\Api\Wiki\Anime\SynonymController;
use App\Http\Controllers\Api\Wiki\Anime\Theme\EntryController;
use App\Http\Controllers\Api\Wiki\Anime\ThemeController;
use App\Http\Controllers\Api\Wiki\Anime\YearController;
use App\Http\Controllers\Api\Wiki\AnimeController;
use App\Http\Controllers\Api\Wiki\ArtistController;
use App\Http\Controllers\Api\Wiki\ExternalResourceController;
use App\Http\Controllers\Api\Wiki\ImageController;
use App\Http\Controllers\Api\Wiki\SearchController;
use App\Http\Controllers\Api\Wiki\SeriesController;
use App\Http\Controllers\Api\Wiki\SongController;
use App\Http\Controllers\Api\Wiki\StudioController;
use App\Http\Controllers\Api\Wiki\VideoController;
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

    // Config Resources
    Route::get('config/flags', [FlagsController::class, 'show'])->name('config.flags.show');
    Route::get('config/wiki', [WikiController::class, 'show'])->name('config.wiki.show');

    // Billing Resources
    Route::apiResource('balance', BalanceController::class)->only(['index', 'show']);
    Route::apiResource('transaction', TransactionController::class)->only(['index', 'show']);

    // Document Resources
    Route::apiResource('page', PageController::class)->only(['index', 'show'])->where(['page' => '[\pL\pM\pN\/_-]+']);

    // Wiki Resources
    Route::apiResource('anime', AnimeController::class)->only(['index', 'show']);
    Route::apiResource('artist', ArtistController::class)->only(['index', 'show']);
    Route::apiResource('image', ImageController::class)->only(['index', 'show']);
    Route::apiResource('resource', ExternalResourceController::class)->only(['index', 'show']);
    Route::apiResource('series', SeriesController::class)->only(['index', 'show']);
    Route::apiResource('song', SongController::class)->only(['index', 'show']);
    Route::apiResource('studio', StudioController::class)->only(['index', 'show']);
    Route::apiResource('video', VideoController::class)->only(['index', 'show']);

    // Anime Resources
    Route::apiResource('animesynonym', SynonymController::class)->only(['index', 'show']);
    Route::apiResource('animetheme', ThemeController::class)->only(['index', 'show']);

    // Anime Year Routes
    Route::get('animeyear', [YearController::class, 'index'])->name('animeyear.index');
    Route::get('animeyear/{year}', [YearController::class, 'show'])->name('animeyear.show');

    // Anime Theme Resources
    Route::apiResource('animethemeentry', EntryController::class)->only(['index', 'show']);
});

Route::fallback(function () {
    abort(404);
});
