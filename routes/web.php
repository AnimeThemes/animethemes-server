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

Route::resource('anime', 'AnimeController');
Route::resource('anime.synonym', 'SynonymController');
Route::resource('anime.theme', 'ThemeController');
Route::resource('anime.theme.entry', 'EntryController');
Route::resource('artist', 'ArtistController');
Route::resource('resource', 'ResourceController');
Route::resource('series', 'SeriesController');
Route::resource('song', 'SongController');
Route::resource('video', 'VideoController')->only(['index', 'show', 'edit', 'update']);