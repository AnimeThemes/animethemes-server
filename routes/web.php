<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Billing\TransparencyController;
use App\Http\Controllers\Document\CommunityController;
use App\Http\Controllers\Document\DonateController;
use App\Http\Controllers\Document\EncodingController;
use App\Http\Controllers\Document\EventController;
use App\Http\Controllers\Document\FaqController;
use App\Http\Controllers\Document\GuidelinesController;
use App\Http\Controllers\Sitemap\CommunitySitemapController;
use App\Http\Controllers\Sitemap\EncodingSitemapController;
use App\Http\Controllers\Sitemap\EventSitemapController;
use App\Http\Controllers\Sitemap\GuidelinesSitemapController;
use App\Http\Controllers\Sitemap\SitemapController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\Wiki\ImageController;
use App\Http\Controllers\Wiki\VideoController;
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

// Home
Route::get('/', [WelcomeController::class, 'show'])->name('welcome');

// Billing
Route::get('transparency', [TransparencyController::class, 'show'])->name('transparency.show');

// Content Streaming
Route::resource('image', ImageController::class)->only('show')
    ->middleware('without_trashed:image');
Route::resource('video', VideoController::class)->only('show')
    ->middleware(['is_video_streaming_allowed', 'without_trashed:video']);

// Documents
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

// Sitemaps
Route::get('/sitemap', [SitemapController::class, 'show'])->name('sitemap');
Route::get('/sitemap/community', [CommunitySitemapController::class, 'show'])->name('sitemap.community');
Route::get('/sitemap/encoding', [EncodingSitemapController::class, 'show'])->name('sitemap.encoding');
Route::get('/sitemap/event', [EventSitemapController::class, 'show'])->name('sitemap.event');
Route::get('/sitemap/guidelines', [GuidelinesSitemapController::class, 'show'])->name('sitemap.guidelines');

// Auth
Route::get('register/{invitation}', [RegisterController::class, 'showRegistrationForm'])
    ->name('register')
    ->middleware(['guest', 'signed', 'has_open_invitation', 'without_trashed:invitation']);
Route::post('register/{invitation}', [RegisterController::class, 'register'])
    ->middleware(['guest', 'signed', 'has_open_invitation', 'without_trashed:invitation']);

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
