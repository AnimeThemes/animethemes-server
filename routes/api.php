<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Spatie\RouteDiscovery\Discovery\Discover;

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

Discover::controllers()->in(app_path('Http/Controllers/Api'));

Route::fallback(function () {
    abort(404);
});
