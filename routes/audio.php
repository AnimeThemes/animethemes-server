<?php

declare(strict_types=1);

use App\Constants\Config\FlagConstants;
use App\Http\Controllers\Wiki\Audio\AudioController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

$isAudioStreamingAllowed = Str::of('is_feature_enabled:')
    ->append(FlagConstants::ALLOW_AUDIO_STREAMS_FLAG_QUALIFIED)
    ->append(',Audio Streaming Disabled')
    ->__toString();

Route::get('/{audio}', [AudioController::class, 'show'])
    ->name('audio.show')
    ->middleware([$isAudioStreamingAllowed, 'record_view:audio']);
