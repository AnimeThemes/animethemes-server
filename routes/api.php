<?php

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

Route::group(['as' => 'api.'], function() {

    // Search Routes
    Route::get('anime/search', 'Api\AnimeController@search');
    Route::get('synonym/search', 'Api\SynonymController@search');
    Route::get('theme/search', 'Api\ThemeController@search');
    Route::get('song/search', 'Api\SongController@search');
    Route::get('artist/search', 'Api\ArtistController@search');
    Route::get('entry/search', 'Api\EntryController@search');
    Route::get('series/search', 'Api\SeriesController@search');

    // Resource Routes
    Route::apiResource('anime', 'Api\AnimeController')->only(['index', 'show']);
    Route::apiResource('synonym', 'Api\SynonymController')->only(['index', 'show']);
    Route::apiResource('theme', 'Api\ThemeController')->only(['index', 'show']);
    Route::apiResource('song', 'Api\SongController')->only(['index', 'show']);
    Route::apiResource('artist', 'Api\ArtistController')->only(['index', 'show']);
    Route::apiResource('resource', 'Api\ExternalResourceController')->only(['index', 'show']);
    Route::apiResource('entry', 'Api\EntryController')->only(['index', 'show']);
    Route::apiResource('series', 'Api\SeriesController')->only(['index', 'show']);
    Route::apiResource('video', 'Api\VideoController')->only(['index', 'show']);
});
