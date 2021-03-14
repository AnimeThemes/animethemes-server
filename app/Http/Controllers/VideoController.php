<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VideoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('without_trashed:video');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function show(Video $video)
    {
        set_time_limit(0);

        views($video)
            ->cooldown(now()->addMinutes(5))
            ->record();

        $response = new StreamedResponse;

        $disposition = $response->headers->makeDisposition('inline', $video->basename);

        $response->headers->replace([
            'Accept-Ranges' => 'bytes',
            'Content-Type' => 'video/webm',
            'Content-Length' => $video->size,
            'Content-Disposition' => $disposition,
        ]);

        $fs = Storage::disk('spaces');

        $response->setCallback(function () use ($fs, $video) {
            $stream = $fs->readStream($video->path);
            if (! is_null($stream)) {
                fpassthru($stream);
                fclose($stream);
            }
        });

        return $response;
    }
}
