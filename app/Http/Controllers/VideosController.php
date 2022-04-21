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

        // Generate temporary link for the object
        $temporaryURL = $fs->temporaryUrl($video->path, now()->addMinutes(5));

        // Get the url information
        $url_scheme = parse_url($temporaryURL, PHP_URL_SCHEME);
        $url_host = parse_url($temporaryURL, PHP_URL_HOST);
        $url_path_query = parse_url($temporaryURL, PHP_URL_PATH).'?'.parse_url($temporaryURL, PHP_URL_QUERY);

        // Construct the new link for the redirect
        $link = '/video_redirect/'.$url_scheme.'/'.$url_host.'/'.$url_path_query;

        // Set the X-ACCEL-REDIRECT header
        $response->headers->set('X-Accel-Redirect', $link);

        return $response;
    }
}
