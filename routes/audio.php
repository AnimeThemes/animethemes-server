<?php

declare(strict_types=1);

use App\Features\AllowAudioStreams;
use App\Http\Controllers\Wiki\Audio\AudioController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

$isAudioStreamingAllowed = Str::of(EnsureFeaturesAreActive::class)
    ->append(':')
    ->append(AllowAudioStreams::class)
    ->__toString();

Route::get('/{audio}', [AudioController::class, 'show'])
    ->name('audio.show')
    ->middleware([$isAudioStreamingAllowed]);
