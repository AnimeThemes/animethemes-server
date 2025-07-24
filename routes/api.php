<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Admin\AnnouncementController;
use App\Http\Controllers\Api\Admin\CurrentFeaturedThemeController;
use App\Http\Controllers\Api\Admin\DumpController;
use App\Http\Controllers\Api\Admin\FeatureController;
use App\Http\Controllers\Api\Admin\FeaturedThemeController;
use App\Http\Controllers\Api\Auth\User\Me\List\MyExternalProfileController;
use App\Http\Controllers\Api\Auth\User\Me\List\MyNotificationController;
use App\Http\Controllers\Api\Auth\User\Me\List\MyPlaylistController;
use App\Http\Controllers\Api\Auth\User\Me\MyController;
use App\Http\Controllers\Api\Document\PageController;
use App\Http\Controllers\Api\List\External\ExternalEntryController;
use App\Http\Controllers\Api\List\ExternalProfileController;
use App\Http\Controllers\Api\List\Playlist\TrackBackwardController;
use App\Http\Controllers\Api\List\Playlist\TrackController;
use App\Http\Controllers\Api\List\Playlist\TrackForwardController;
use App\Http\Controllers\Api\List\PlaylistBackwardController;
use App\Http\Controllers\Api\List\PlaylistController;
use App\Http\Controllers\Api\List\PlaylistForwardController;
use App\Http\Controllers\Api\Pivot\List\PlaylistImageController;
use App\Http\Controllers\Api\Pivot\Wiki\AnimeImageController;
use App\Http\Controllers\Api\Pivot\Wiki\AnimeResourceController;
use App\Http\Controllers\Api\Pivot\Wiki\AnimeSeriesController;
use App\Http\Controllers\Api\Pivot\Wiki\AnimeStudioController;
use App\Http\Controllers\Api\Pivot\Wiki\AnimeThemeEntryVideoController;
use App\Http\Controllers\Api\Pivot\Wiki\ArtistImageController;
use App\Http\Controllers\Api\Pivot\Wiki\ArtistMemberController;
use App\Http\Controllers\Api\Pivot\Wiki\ArtistResourceController;
use App\Http\Controllers\Api\Pivot\Wiki\ArtistSongController;
use App\Http\Controllers\Api\Pivot\Wiki\SongResourceController;
use App\Http\Controllers\Api\Pivot\Wiki\StudioImageController;
use App\Http\Controllers\Api\Pivot\Wiki\StudioResourceController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\Wiki\Anime\SynonymController;
use App\Http\Controllers\Api\Wiki\Anime\Theme\EntryController;
use App\Http\Controllers\Api\Wiki\Anime\ThemeController;
use App\Http\Controllers\Api\Wiki\Anime\YearController;
use App\Http\Controllers\Api\Wiki\AnimeController;
use App\Http\Controllers\Api\Wiki\ArtistController;
use App\Http\Controllers\Api\Wiki\AudioController;
use App\Http\Controllers\Api\Wiki\ExternalResourceController;
use App\Http\Controllers\Api\Wiki\GroupController;
use App\Http\Controllers\Api\Wiki\ImageController;
use App\Http\Controllers\Api\Wiki\SeriesController;
use App\Http\Controllers\Api\Wiki\Song\MembershipController;
use App\Http\Controllers\Api\Wiki\Song\PerformanceController;
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
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

if (! function_exists('apiResource')) {
    /**
     * API resource route registration with soft-delete routes.
     *
     * @param  string  $name
     * @param  string  $controller
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

        return $uri->__toString();
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
     */
    function apiPivotResource(string $name, string $related, string $foreign, string $controller): void
    {
        Route::get($name, [$controller, 'index'])->name("$name.index");
        Route::post(apiPivotResourceUri($name, $related, $foreign), [$controller, 'store'])->name("$name.store");
        Route::get(apiPivotResourceUri($name, $related, $foreign), [$controller, 'show'])->name("$name.show");
        Route::delete(apiPivotResourceUri($name, $related, $foreign), [$controller, 'destroy'])->name("$name.destroy");
    }
}

if (! function_exists('apiEditablePivotResource')) {
    /**
     * API pivot resource route registration with update action.
     *
     * @param  string  $name
     * @param  string  $related
     * @param  string  $foreign
     * @param  string  $controller
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
            ->__toString();
    }
}

// Admin Routes
Route::apiResource('announcement', AnnouncementController::class);
Route::apiResource('dump', DumpController::class);
Route::apiResource('featuredtheme', FeaturedThemeController::class);
Route::get('current/featuredtheme', [CurrentFeaturedThemeController::class, 'show'])
    ->name('featuredtheme.current.show');
Route::apiResource('feature', FeatureController::class)
    ->only(['index', 'show', 'update']);

// Auth Routes
Route::get('/me', [MyController::class, 'show'])->name('me.show');
Route::get('/me/externalprofile', [MyExternalProfileController::class, 'index'])->name('me.externalprofile.index');
Route::get('/me/notification', [MyNotificationController::class, 'index'])->name('me.notification.index');
Route::match(['put', 'patch'], '/me/notification/readall', [MyNotificationController::class, 'readall'])->name('me.notification.readall');
Route::match(['put', 'patch'], '/me/notification/{notification}/read', [MyNotificationController::class, 'read'])->name('me.notification.read');
Route::match(['put', 'patch'], '/me/notification/{notification}/unread', [MyNotificationController::class, 'unread'])->name('me.notification.unread');
Route::get('/me/playlist', [MyPlaylistController::class, 'index'])->name('me.playlist.index');

// Document Routes
apiResourceWhere('page', PageController::class, ['page' => '[\pL\pM\pN\/_-]+']);

// List Routes
// External Routes
Route::apiResource('externalprofile', ExternalProfileController::class);
Route::apiResource('externalprofile.externalentry', ExternalEntryController::class)
    ->scoped();

// Playlist Routes
Route::apiResource('playlist', PlaylistController::class);
Route::apiResource('playlist.track', TrackController::class)
    ->scoped();

Route::get('playlist/{playlist}/forward', [PlaylistForwardController::class, 'index'])
    ->name('playlist.forward');

Route::get('playlist/{playlist}/track/{track}/forward', [TrackForwardController::class, 'index'])
    ->name('playlist.track.forward');

Route::get('playlist/{playlist}/backward', [PlaylistBackwardController::class, 'index'])
    ->name('playlist.backward');

Route::get('playlist/{playlist}/track/{track}/backward', [TrackBackwardController::class, 'index'])
    ->name('playlist.track.backward');

// Pivot Routes
apiPivotResource('animeimage', 'anime', 'image', AnimeImageController::class);
apiEditablePivotResource('animeresource', 'anime', 'resource', AnimeResourceController::class);
apiPivotResource('animeseries', 'anime', 'series', AnimeSeriesController::class);
apiPivotResource('animestudio', 'anime', 'studio', AnimeStudioController::class);
apiPivotResource('animethemeentryvideo', 'animethemeentry', 'video', AnimeThemeEntryVideoController::class);
apiPivotResource('artistimage', 'artist', 'image', ArtistImageController::class);
apiEditablePivotResource('artistmember', 'artist', 'member', ArtistMemberController::class);
apiEditablePivotResource('artistresource', 'artist', 'resource', ArtistResourceController::class);
apiPivotResource('playlistimage', 'playlist', 'image', PlaylistImageController::class);
apiEditablePivotResource('songresource', 'song', 'resource', SongResourceController::class);
apiPivotResource('studioimage', 'studio', 'image', StudioImageController::class);
apiEditablePivotResource('studioresource', 'studio', 'resource', StudioResourceController::class);
apiEditablePivotResource('artistsong', 'artist', 'song', ArtistSongController::class);

// Wiki Routes
apiResource('anime', AnimeController::class);
apiResource('artist', ArtistController::class);
apiResource('audio', AudioController::class);
apiResource('group', GroupController::class);
apiResource('image', ImageController::class);
apiResource('membership', MembershipController::class);
apiResource('performance', PerformanceController::class);
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
