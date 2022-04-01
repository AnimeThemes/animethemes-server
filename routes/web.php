<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Billing\TransparencyController;
use App\Http\Controllers\Sitemap\SitemapController;
use App\Http\Controllers\WelcomeController;
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
Route::resource('video', VideoController::class)->only('show')
    ->middleware(['is_video_streaming_allowed', 'without_trashed:video', 'record_view:video']);

// Sitemaps
Route::get('/sitemap', [SitemapController::class, 'show'])->name('sitemap');

// Auth
Route::get('register/{invitation}', [RegisterController::class, 'showRegistrationForm'])
    ->name('register')
    ->middleware(['guest', 'signed', 'has_open_invitation', 'without_trashed:invitation']);
Route::post('register/{invitation}', [RegisterController::class, 'register'])
    ->middleware(['guest', 'signed', 'has_open_invitation', 'without_trashed:invitation']);

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
