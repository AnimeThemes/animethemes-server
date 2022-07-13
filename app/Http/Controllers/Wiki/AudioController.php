<?php

declare(strict_types=1);

namespace App\Http\Controllers\Wiki;

use App\Http\Controllers\Controller;
use App\Models\Wiki\Audio;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

/**
 * Class AudioController.
 */
class AudioController extends Controller
{
    /**
     * Stream audio through nginx internal redirect.
     *
     * @param  Audio  $audio
     * @return Response
     */
    public function show(Audio $audio): Response
    {
        $fs = Storage::disk('audios');

        // Generate temporary link for the object
        $temporaryURL = $fs->temporaryUrl($audio->path, now()->addMinutes(5));

        // Get the url information
        $url_scheme = parse_url($temporaryURL, PHP_URL_SCHEME);
        $url_host = parse_url($temporaryURL, PHP_URL_HOST);
        $url_path_query = parse_url($temporaryURL, PHP_URL_PATH).'?'.parse_url($temporaryURL, PHP_URL_QUERY);

        // Construct the new link for the redirect
        $link = "/audio_redirect/$url_scheme/$url_host$url_path_query";

        $response = new Response();

        $disposition = $response->headers->makeDisposition('inline', $audio->basename);

        return $response->withHeaders([
            'Accept-Ranges' => 'bytes',
            'Content-Type' => $audio->mimetype,
            'Content-Length' => $audio->size,
            'Content-Disposition' => $disposition,
            'X-Accel-Redirect' => $link,
        ]);
    }
}
