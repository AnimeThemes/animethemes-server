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

// Search Routes
Route::get('search', [SearchController::class, 'show'])->name('search.show');

// Config Resources
Route::get('config/flags', [FlagsController::class, 'show'])->name('config.flags.show');
Route::get('config/wiki', [WikiController::class, 'show'])->name('config.wiki.show');

// Billing Resources
Route::apiResource('balance', BalanceController::class)->only(['index', 'show']);
Route::apiResource('transaction', TransactionController::class)->only(['index', 'show']);

// Wiki Resources
Route::apiResource('image', ImageController::class)->only(['index', 'show']);

// Anime Year Routes
Route::get('animeyear', [YearController::class, 'index'])->name('animeyear.index');
Route::get('animeyear/{year}', [YearController::class, 'show'])->name('animeyear.show');

Route::group([['middleware' => ['auth:sanctum' => ['except' => ['index', 'show']]]]], function () {
    Route::apiResources([
        // Admin Resources
        'announcement' => AnnouncementController::class,

        // Wiki Resources
        'anime' => AnimeController::class,
        'artist' => ArtistController::class,
        'resource' => ExternalResourceController::class,
        'series' => SeriesController::class,
        'song' => SongController::class,
        'studio' => StudioController::class,
        'video' => VideoController::class,

        // Anime Resources
        'animesynonym' => SynonymController::class,
        'animetheme' => ThemeController::class,

        // Anime Theme Resources
        'animethemeentry' => EntryController::class,
    ]);

    // Document Resources
    Route::apiResource('page', PageController::class)->where(['page' => '[\pL\pM\pN\/_-]+']);

    // Restore Admin Resources
    Route::patch('restore/announcement/{announcement}', [AnnouncementController::class, 'restore'])->name('announcement.restore');

    // Restore Document Resources
    Route::patch('restore/page/{page}', [PageController::class, 'restore'])->where(['page' => '[\pL\pM\pN\/_-]+'])->name('page.restore');

    // Restore Wiki Resources
    Route::patch('restore/anime/{anime}', [AnimeController::class, 'restore'])->name('anime.restore');
    Route::patch('restore/artist/{artist}', [ArtistController::class, 'restore'])->name('artist.restore');
    Route::patch('restore/resource/{resource}', [ExternalResourceController::class, 'restore'])->name('resource.restore');
    Route::patch('restore/series/{series}', [SeriesController::class, 'restore'])->name('series.restore');
    Route::patch('restore/song/{song}', [SongController::class, 'restore'])->name('song.restore');
    Route::patch('restore/studio/{studio}', [StudioController::class, 'restore'])->name('studio.restore');
    Route::patch('restore/video/{video}', [VideoController::class, 'restore'])->name('video.restore');

    // Restore Anime Resources
    Route::patch('restore/animesynonym/{animesynonym}', [SynonymController::class, 'restore'])->name('animesynonym.restore');
    Route::patch('restore/animetheme/{animetheme}', [ThemeController::class, 'restore'])->name('animetheme.restore');

    // Restore Anime Theme Resources
    Route::patch('restore/animethemeentry/{animethemeentry}', [EntryController::class, 'restore'])->name('animethemeentry.restore');

    // Force Delete Admin Resources
    Route::delete('forceDelete/announcement/{announcement}', [AnnouncementController::class, 'forceDelete'])->name('announcement.forceDelete');

    // Force Delete Document Resources
    Route::delete('forceDelete/page/{page}', [PageController::class, 'forceDelete'])->where(['page' => '[\pL\pM\pN\/_-]+'])->name('page.forceDelete');

    // Force Delete Wiki Resources
    Route::delete('forceDelete/anime/{anime}', [AnimeController::class, 'forceDelete'])->name('anime.forceDelete');
    Route::delete('forceDelete/artist/{artist}', [ArtistController::class, 'forceDelete'])->name('artist.forceDelete');
    Route::delete('forceDelete/resource/{resource}', [ExternalResourceController::class, 'forceDelete'])->name('resource.forceDelete');
    Route::delete('forceDelete/series/{series}', [SeriesController::class, 'forceDelete'])->name('series.forceDelete');
    Route::delete('forceDelete/song/{song}', [SongController::class, 'forceDelete'])->name('song.forceDelete');
    Route::delete('forceDelete/studio/{studio}', [StudioController::class, 'forceDelete'])->name('studio.forceDelete');
    Route::delete('forceDelete/video/{video}', [VideoController::class, 'forceDelete'])->name('video.forceDelete');

    // Force Delete Anime Resources
    Route::delete('forceDelete/animesynonym/{animesynonym}', [SynonymController::class, 'forceDelete'])->name('animesynonym.forceDelete');
    Route::delete('forceDelete/animetheme/{animetheme}', [ThemeController::class, 'forceDelete'])->name('animetheme.forceDelete');

    // Force Delete Anime Theme Resources
    Route::delete('forceDelete/animethemeentry/{animethemeentry}', [EntryController::class, 'forceDelete'])->name('animethemeentry.forceDelete');
});

Route::fallback(function () {
    abort(404);
});
