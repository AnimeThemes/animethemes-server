<?php

namespace App\Http\Controllers;

use App\Concerns\Http\Controllers\StreamsContent;
use App\Models\Video;

class VideoController extends Controller
{
    use StreamsContent;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['is_video_streaming_allowed', 'without_trashed:video']);
    }

    /**
     * Stream video.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function show(Video $video)
    {
        set_time_limit(0);

        views($video)
            ->cooldown(now()->addMinutes(5))
            ->record();

        return $this->streamContent($video);
    }
}
