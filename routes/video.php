<?php

declare(strict_types=1);

use App\Constants\Config\FlagConstants;
use App\Http\Controllers\Wiki\Video\VideoController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

$isVideoStreamingAllowed = Str::of('is_feature_enabled:')
    ->append(FlagConstants::ALLOW_VIDEO_STREAMS_FLAG_QUALIFIED)
    ->append(',Video Streaming Disabled')
    ->__toString();

Route::get('/{video}', [VideoController::class, 'show'])
    ->name('video.show')
    ->middleware([$isVideoStreamingAllowed, 'record_view:video']);
