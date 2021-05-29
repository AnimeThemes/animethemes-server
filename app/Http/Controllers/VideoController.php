<?php

namespace App\Http\Controllers;

use App\Concerns\Http\Controllers\StreamsContent;
use App\Models\Video;

class VideoController extends Controller
{
    use StreamsContent;

    /**
     * Stream video.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function show(Video $video)
    {
        views($video)
            ->cooldown(now()->addMinutes(5))
            ->record();

        return $this->streamContent($video);
    }
}
