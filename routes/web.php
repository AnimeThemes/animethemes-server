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
    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@register');
    Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->name('verification.verify');
    Route::post('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');
});
