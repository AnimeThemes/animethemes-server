<?php

declare(strict_types=1);

use App\Features\AllowAudioStreams;
use App\Http\Controllers\Wiki\Audio\AudioController;
use Illuminate\Support\Facades\Route;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

Route::get('/{audio}', [AudioController::class, 'show'])
    ->name('audio.show')
    ->middleware(EnsureFeaturesAreActive::using(AllowAudioStreams::class));
