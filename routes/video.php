<?php

declare(strict_types=1);

use App\Http\Controllers\Wiki\Video\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/{video}', [VideoController::class, 'show'])
    ->name('video.show')
    ->middleware(['is_video_streaming_allowed', 'without_trashed:video', 'record_view:video']);
