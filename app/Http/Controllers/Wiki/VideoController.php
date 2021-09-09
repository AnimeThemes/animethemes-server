<?php

declare(strict_types=1);

namespace App\Http\Controllers\Wiki;

use App\Models\Wiki\Video;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class VideoController.
 */
class VideoController extends StreamableController
{
    /**
     * Stream video.
     *
     * @param  Video  $video
     * @return StreamedResponse
     */
    public function show(Video $video): StreamedResponse
    {
        return $this->streamContent($video);
    }
}
