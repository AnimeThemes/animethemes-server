<?php

declare(strict_types=1);

use App\Features\AllowVideoStreams;
use App\Http\Controllers\Wiki\Video\VideoController;
use Illuminate\Support\Facades\Route;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

Route::get('/{video}', [VideoController::class, 'show'])
    ->name('video.show')
    ->middleware([EnsureFeaturesAreActive::using(AllowVideoStreams::class), 'throttle:video']);
