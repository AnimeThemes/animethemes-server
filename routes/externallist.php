<?php

declare(strict_types=1);

use App\Http\Controllers\List\External\ExternalTokenAuthController;
use App\Http\Controllers\List\External\ExternalTokenCallbackController;
use App\Http\Controllers\List\SyncExternalProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| External List Routes
|--------------------------------------------------------------------------
|
| Here is where you can register any routes about external lists. These
| routes are loaded by the RouteServiceProvider and use the api subdomain,
| but all of them will be assigned to the "web" middleware group instead.
| Make something great!
|
*/

Route::get('externaltoken/auth', [ExternalTokenAuthController::class, 'index'])
    ->name('externaltoken.auth');

Route::get('externaltoken/callback', [ExternalTokenCallbackController::class, 'index'])
    ->name('externaltoken.callback');

Route::get('externalprofile/{externalprofile}/sync', [SyncExternalProfileController::class, 'show'])
    ->name('externalprofile.sync.show');

Route::post('externalprofile/{externalprofile}/sync', [SyncExternalProfileController::class, 'store'])
    ->name('externalprofile.sync.store');
