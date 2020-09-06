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
    Route::get('search', 'Api\BaseController@search');

    // Resource Routes
    Route::apiResource('anime', 'Api\AnimeController')->only(['index', 'show']);
    Route::apiResource('artist', 'Api\ArtistController')->only(['index', 'show']);
    Route::apiResource('entry', 'Api\EntryController')->only(['index', 'show']);
    Route::apiResource('resource', 'Api\ExternalResourceController')->only(['index', 'show']);
    Route::apiResource('series', 'Api\SeriesController')->only(['index', 'show']);
    Route::apiResource('song', 'Api\SongController')->only(['index', 'show']);
    Route::apiResource('synonym', 'Api\SynonymController')->only(['index', 'show']);
    Route::apiResource('theme', 'Api\ThemeController')->only(['index', 'show']);
    Route::apiResource('video', 'Api\VideoController')->only(['index', 'show']);

    // Year Routes
    Route::get('year/{year}', 'Api\YearController@show');
});
