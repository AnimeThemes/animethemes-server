<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Admin\AnnouncementController;
use App\Http\Controllers\Api\Admin\DumpController;
use App\Http\Controllers\Api\Auth\User\Me\List\MyPlaylistController;
use App\Http\Controllers\Api\Auth\User\Me\MyController;
use App\Http\Controllers\Api\Billing\BalanceController;
use App\Http\Controllers\Api\Billing\TransactionController;
use App\Http\Controllers\Api\Billing\TransparencyController;
use App\Http\Controllers\Api\Config\FlagsController;
use App\Http\Controllers\Api\Config\WikiController;
use App\Http\Controllers\Api\Document\PageController;
use App\Http\Controllers\Api\List\Playlist\BackwardController;
use App\Http\Controllers\Api\List\Playlist\ForwardController;
use App\Http\Controllers\Api\List\Playlist\TrackController;
use App\Http\Controllers\Api\List\PlaylistController;
use App\Http\Controllers\Api\Pivot\Wiki\AnimeImageController;
use App\Http\Controllers\Api\Pivot\Wiki\AnimeResourceController;
use App\Http\Controllers\Api\Wiki\Anime\SynonymController;
use App\Http\Controllers\Api\Wiki\Anime\Theme\EntryController;
use App\Http\Controllers\Api\Wiki\Anime\ThemeController;
use App\Http\Controllers\Api\Wiki\Anime\YearController;
use App\Http\Controllers\Api\Wiki\AnimeController;
use App\Http\Controllers\Api\Wiki\ArtistController;
use App\Http\Controllers\Api\Wiki\AudioController;
use App\Http\Controllers\Api\Wiki\ExternalResourceController;
use App\Http\Controllers\Api\Wiki\ImageController;
use App\Http\Controllers\Api\Wiki\SearchController;
use App\Http\Controllers\Api\Wiki\SeriesController;
use App\Http\Controllers\Api\Wiki\SongController;
use App\Http\Controllers\Api\Wiki\StudioController;
use App\Http\Controllers\Api\Wiki\Video\ScriptController;
use App\Http\Controllers\Api\Wiki\VideoController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

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

if (! function_exists('apiResource')) {
    /**
     * API resource route registration with soft-delete routes.
     *
     * @param  string  $name
     * @param  string  $controller
     * @return void
     */
    function apiResource(string $name, string $controller): void
    {
        Route::apiResource($name, $controller)->withTrashed();

        Route::patch(apiResourceUri('restore', $name), [$controller, 'restore'])
            ->name("$name.restore")
            ->withTrashed();

        Route::delete(apiResourceUri('forceDelete', $name), [$controller, 'forceDelete'])
            ->name("$name.forceDelete")
            ->withTrashed();
    }
}

if (! function_exists('apiResourceWhere')) {
    /**
     * API resource route registration with soft-delete routes & custom constraints.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  mixed  $wheres
     * @return void
     */
    function apiResourceWhere(string $name, string $controller, mixed $wheres): void
    {
        Route::apiResource($name, $controller)
            ->where($wheres)
            ->withTrashed();

        Route::patch(apiResourceUri('restore', $name), [$controller, 'restore'])
            ->name("$name.restore")
            ->where($wheres)
            ->withTrashed();

        Route::delete(apiResourceUri('forceDelete', $name), [$controller, 'forceDelete'])
            ->name("$name.forceDelete")
            ->where($wheres)
            ->withTrashed();
    }
}

if (! function_exists('apiScopedResource')) {
    /**
     * API scoped resource route registration with soft-delete routes.
     *
     * @param  string  $name
     * @param  string  $controller
     * @return void
     */
    function apiScopedResource(string $name, string $controller): void
    {
        Route::apiResource($name, $controller)
            ->scoped()
            ->withTrashed();

        Route::patch(apiResourceUri('restore', $name), [$controller, 'restore'])
            ->name("$name.restore")
            ->scopeBindings()
            ->withTrashed();

        Route::delete(apiResourceUri('forceDelete', $name), [$controller, 'forceDelete'])
            ->name("$name.forceDelete")
            ->scopeBindings()
            ->withTrashed();
    }
}

if (! function_exists('apiResourceUri')) {
    /**
     * Uri for ability.
     * Note: Route model binding is not resolved correctly for "ability/$name/{$name}" so we need a string builder.
     *
     * @param  string  $ability
     * @param  string  $name
     * @return string
     */
    function apiResourceUri(string $ability, string $name): string
    {
        $uri = Str::of($ability);

        $nameParts = Str::of($name)->explode('.');

        foreach ($nameParts as $namePart) {
            $uri = $uri->append('/')
                ->append($namePart)
                ->append('/{')
                ->append($namePart)
                ->append('}');
        }

        return $uri->toString();
    }
}

if (! function_exists('apiPivotResource')) {
    /**
     * API pivot resource route registration.
     *
     * @param  string  $name
     * @param  string  $related
     * @param  string  $foreign
     * @param  string  $controller
     * @return void
     */
    function apiPivotResource(string $name, string $related, string $foreign, string $controller): void
    {
        Route::get($name, [$controller, 'index'])->name("$name.index");
        Route::post($name, [$controller, 'store'])->name("$name.store");
        Route::get(apiPivotResourceUri($name, $related, $foreign), [$controller, 'show'])->name("$name.show");
        Route::delete(apiPivotResourceUri($name, $related, $foreign), [$controller, 'destroy'])->name("$name.destroy");
    }
}

if (! function_exists('apiEditablePivotResource')) {
    /**
     * API pivot resource route registration.
     *
     * @param  string  $name
     * @param  string  $related
     * @param  string  $foreign
     * @param  string  $controller
     * @return void
     */
    function apiEditablePivotResource(string $name, string $related, string $foreign, string $controller): void
    {
        apiPivotResource($name, $related, $foreign, $controller);
        Route::match(['put', 'patch'], apiPivotResourceUri($name, $related, $foreign), [$controller, 'update'])->name("$name.update");
    }
}

if (! function_exists('apiPivotResourceUri')) {
    /**
     * Uri for ability.
     * Note: Route model binding is not resolved correctly for "$name/{$related}/{$foreign}" so we need a string builder.
     *
     * @param  string  $name
     * @param  string  $related
     * @param  string  $foreign
     * @return string
     */
    function apiPivotResourceUri(string $name, string $related, string $foreign): string
    {
        return Str::of($name)
            ->append('/{')
            ->append($related)
            ->append('}/{')
            ->append($foreign)
            ->append('}')
            ->toString();
    }
}

// Admin Routes
apiResource('announcement', AnnouncementController::class);
apiResource('dump', DumpController::class);

// Auth Routes
Route::get('/me', [MyController::class, 'show'])->name('me.show');
Route::get('/me/playlist', [MyPlaylistController::class, 'index'])->name('me.playlist.index');

// Billing Routes
apiResource('balance', BalanceController::class);
apiResource('transaction', TransactionController::class);
Route::get('transparency', [TransparencyController::class, 'show'])->name('transparency.show');

// Config Routes
Route::get('config/flags', [FlagsController::class, 'show'])->name('config.flags.show');
Route::get('config/wiki', [WikiController::class, 'show'])->name('config.wiki.show');

// Document Routes
apiResourceWhere('page', PageController::class, ['page' => '[\pL\pM\pN\/_-]+']);

// List Routes
apiResource('playlist', PlaylistController::class);
apiScopedResource('playlist.track', TrackController::class);
Route::get('playlist/{playlist}/forward', [ForwardController::class, 'index'])->name('playlist.forward');
Route::get('playlist/{playlist}/backward', [BackwardController::class, 'index'])->name('playlist.backward');

// Pivot Routes
apiPivotResource('animeimage', 'anime', 'image', AnimeImageController::class);
apiEditablePivotResource('animeresource', 'anime', 'resource', AnimeResourceController::class);

// Wiki Routes
apiResource('anime', AnimeController::class);
apiResource('artist', ArtistController::class);
apiResource('audio', AudioController::class);
apiResource('image', ImageController::class);
apiResource('resource', ExternalResourceController::class);
Route::get('search', [SearchController::class, 'show'])->name('search.show');
apiResource('series', SeriesController::class);
apiResource('song', SongController::class);
apiResource('studio', StudioController::class);
apiResource('video', VideoController::class);

// Anime Routes
apiResource('animesynonym', SynonymController::class);
apiResource('animetheme', ThemeController::class);
Route::get('animeyear', [YearController::class, 'index'])->name('animeyear.index');
Route::get('animeyear/{year}', [YearController::class, 'show'])->name('animeyear.show');

// Anime Theme Routes
apiResource('animethemeentry', EntryController::class);

// Video Routes
apiResource('videoscript', ScriptController::class);

Route::fallback(function () {
    abort(404);
});
