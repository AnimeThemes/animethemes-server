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
Route::resource('video', 'VideosController', ['only' => [
    'index', 'show'
]]);
Route::get('/sitemap', 'SitemapController@index');
Route::get('/sitemap/videos', 'SitemapController@videos')->name('video_sitemap');

Route::resource('series', 'SeriesController');