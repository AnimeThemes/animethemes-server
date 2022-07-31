<?php

declare(strict_types=1);

namespace App\Http\Controllers\Wiki;

use App\Http\Controllers\Controller;
use App\Models\Wiki\Video;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

/**
 * Class VideoController.
 */
class VideoController extends Controller
{
    /**
     * Stream video through nginx internal redirect.
     *
     * @param  Video  $video
     * @return Response
     */
    public function show(Video $video): Response
    {
        $fs = Storage::disk(Config::get('video.disk'));

        // Generate temporary link for the object
        $temporaryURL = $fs->temporaryUrl($video->path, now()->addMinutes(5));

        // Get the url information
        $url_scheme = parse_url($temporaryURL, PHP_URL_SCHEME);
        $url_host = parse_url($temporaryURL, PHP_URL_HOST);
        $url_path_query = parse_url($temporaryURL, PHP_URL_PATH).'?'.parse_url($temporaryURL, PHP_URL_QUERY);

        // Construct the new link for the redirect
        $link = "/video_redirect/$url_scheme/$url_host$url_path_query";

        $response = new Response();

        $disposition = $response->headers->makeDisposition('inline', $video->basename);

        return $response->withHeaders([
            'Accept-Ranges' => 'bytes',
            'Content-Type' => $video->mimetype,
            'Content-Length' => $video->size,
            'Content-Disposition' => $disposition,
            'X-Accel-Redirect' => $link,
        ]);
    }
}
