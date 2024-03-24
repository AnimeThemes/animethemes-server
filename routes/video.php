<?php

declare(strict_types=1);

use App\Features\AllowVideoStreams;
use App\Http\Controllers\Wiki\Video\VideoController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

$isVideoStreamingAllowed = Str::of(EnsureFeaturesAreActive::class)
    ->append(':')
    ->append(AllowVideoStreams::class)
    ->__toString();

Route::get('/{video}', [VideoController::class, 'show'])
    ->name('video.show')
    ->middleware([$isVideoStreamingAllowed, 'record_view:video', 'throttle:video']);
