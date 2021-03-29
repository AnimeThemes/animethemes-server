<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Document\CommunityController;
use App\Http\Controllers\Document\DonateController;
use App\Http\Controllers\Document\EncodingController;
use App\Http\Controllers\Document\EventController;
use App\Http\Controllers\Document\FaqController;
use App\Http\Controllers\Document\GuidelinesController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\Sitemap\CommunitySitemapController;
use App\Http\Controllers\Sitemap\EncodingSitemapController;
use App\Http\Controllers\Sitemap\EventSitemapController;
use App\Http\Controllers\Sitemap\GuidelinesSitemapController;
use App\Http\Controllers\Sitemap\SitemapController;
use App\Http\Controllers\TransparencyController;
use App\Http\Controllers\WelcomeController;
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

Route::get('/', [WelcomeController::class, 'index'])->name('welcome.index');

Route::get('transparency', [TransparencyController::class, 'show'])->name('transparency.show');
Route::get('donate', [DonateController::class, 'show'])->name('donate.show');
Route::get('faq', [FaqController::class, 'show'])->name('faq.show');

Route::get('community', [CommunityController::class, 'index'])->name('community.index');
Route::get('community/{docName}', [CommunityController::class, 'show'])->name('community.show');

Route::get('encoding', [EncodingController::class, 'index'])->name('encoding.index');
Route::get('encoding/{docName}', [EncodingController::class, 'show'])->name('encoding.show');

Route::get('event', [EventController::class, 'index'])->name('event.index');
Route::get('event/{docName}', [EventController::class, 'show'])->name('event.show');

Route::get('guidelines', [GuidelinesController::class, 'index'])->name('guidelines.index');
Route::get('guidelines/{docName}', [GuidelinesController::class, 'show'])->name('guidelines.show');

Route::get('/sitemap', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap/community', [CommunitySitemapController::class, 'show'])->name('sitemap.community');
Route::get('/sitemap/encoding', [EncodingSitemapController::class, 'show'])->name('sitemap.encoding');
Route::get('/sitemap/event', [EventSitemapController::class, 'show'])->name('sitemap.event');
Route::get('/sitemap/guidelines', [GuidelinesSitemapController::class, 'show'])->name('sitemap.guidelines');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

Route::resource('image', ImageController::class)->only('show');

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
