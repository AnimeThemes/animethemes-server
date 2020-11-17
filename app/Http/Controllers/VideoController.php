<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VideoController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|void
     */
    public function show(Video $video)
    {
        if (Config::get('app.allow_video_streams', false)) {
            set_time_limit(0);

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
}
