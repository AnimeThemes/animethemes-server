<?php

use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::get('{video}', [VideoController::class, 'show'])->name('video.show');

Route::get('/', function () {
    return redirect(Config::get('app.url'));
});
