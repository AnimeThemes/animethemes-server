<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DonateController;
use App\Http\Controllers\EncodingController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\GuidelinesController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\SitemapController;
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

Route::get('encoding', [EncodingController::class, 'index'])->name('encoding.index');
Route::get('encoding/{docName}', [EncodingController::class, 'show'])->name('encoding.show');

Route::get('guidelines', [GuidelinesController::class, 'index'])->name('guidelines.index');
Route::get('guidelines/{docName}', [GuidelinesController::class, 'show'])->name('guidelines.show');

Route::get('/sitemap', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap/encoding', [SitemapController::class, 'encoding'])->name('sitemap.encoding');
Route::get('/sitemap/guidelines', [SitemapController::class, 'guidelines'])->name('sitemap.guidelines');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

Route::resource('image', ImageController::class)->only('show');

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
