<?php

declare(strict_types=1);

namespace App\Http\Controllers\Wiki\Video;

use App\Actions\Http\StreamAction;
use App\Actions\Http\Wiki\Video\VideoNginxStreamAction;
use App\Actions\Http\Wiki\Video\VideoResponseStreamAction;
use App\Constants\Config\VideoConstants;
use App\Enums\Http\StreamingMethod;
use App\Http\Controllers\Controller;
use App\Models\Wiki\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class VideoController extends Controller
{
    /**
     * Stream video through configured streaming method.
     *
     * @param  Video  $video
     * @return Response
     */
    public function show(Video $video, Request $request): Response
    {
        /** @var StreamAction $action */
        $action = match (Config::get(VideoConstants::STREAMING_METHOD_QUALIFIED)) {
            StreamingMethod::RESPONSE->value => new VideoResponseStreamAction($video),
            StreamingMethod::NGINX->value => new VideoNginxStreamAction($video),
            default => throw new RuntimeException('VIDEO_STREAMING_METHOD must be specified in your .env file'),
        };

        // If the "download" query param is set we want to force the browser to download the file.
        // Otherwise, it should be shown inline for direct playback.
        $disposition = $request->has('download')
            ? 'attachment'
            : 'inline';

        return $action->stream($disposition);
    }
}
