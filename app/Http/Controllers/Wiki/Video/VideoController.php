<?php

declare(strict_types=1);

namespace App\Http\Controllers\Wiki\Video;

use App\Constants\Config\VideoConstants;
use App\Http\Controllers\Wiki\StreamableController;
use App\Models\Wiki\Video;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class VideoController.
 */
class VideoController extends StreamableController
{
    /**
     * Stream video through configured streaming method.
     *
     * @param  Video  $video
     * @return Response|StreamedResponse
     */
    public function show(Video $video): Response|StreamedResponse
    {
        return match (Config::get(VideoConstants::STREAMING_METHOD_QUALIFIED)) {
            'response' => $this->throughResponse($video),
            'nginx' => $this->throughNginx($video),
            default => throw new RuntimeException('VIDEO_STREAMING_METHOD must be specified in your .env file'),
        };
    }

    /**
     * Get the filesystem disk that hosts the streamable model.
     *
     * @return string
     */
    protected function disk(): string
    {
        return Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED);
    }

    /**
     * Get the location of the nginx internal redirect.
     *
     * @return string
     */
    protected function nginxRedirect(): string
    {
        return Config::get(VideoConstants::NGINX_REDIRECT_QUALIFIED);
    }
}
