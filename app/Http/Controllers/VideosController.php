<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VideosController extends Controller
{
    public function show($alias)
    {
        set_time_limit(0);

        $video = Video::where('basename', $alias)->orWhere('filename', $alias)->first();

        // Rather than let an error be thrown, we will instead redirect to the home screen.
        // We are doing this to try and prevent the overzealous CDN from caching the errors.
        // We don't initiate a session for requesting video, so we can't flash a message.
        if ($video == null) {
            return redirect()->route('welcome');
        }

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
            fpassthru($stream);
            fclose($stream);
        });

        return $response;
    }
}
