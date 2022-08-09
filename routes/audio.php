<?php

declare(strict_types=1);

use App\Http\Controllers\Wiki\Audio\AudioController;
use Illuminate\Support\Facades\Route;

Route::get('/{audio}', [AudioController::class, 'show'])
    ->name('audio.show')
    ->middleware(['is_audio_streaming_allowed', 'without_trashed:audio', 'record_view:audio']);
