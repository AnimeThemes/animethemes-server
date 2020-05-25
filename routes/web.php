<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'WelcomeController@do')->name('welcome');
Route::get('/sitemap', 'SitemapController@index');
Route::get('/sitemap/videos', 'SitemapController@videos')->name('video_sitemap');

// We only want to enable middleware necessary for model-route binding
// We don't need the default web middleware stack for public-facing pages
Route::group(['middleware' => ['wiki']], function() {
    Route::resource('anime', 'AnimeController')->only(['index', 'show']);
    Route::resource('artist', 'ArtistController')->only(['index', 'show']);
    Route::resource('series', 'SeriesController')->only(['index', 'show']);
    Route::resource('video', 'VideoController')->only(['index', 'show']);
});

Route::group(['middleware' => ['web']], function() {
    Auth::routes(['verify' => true]);
});
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
