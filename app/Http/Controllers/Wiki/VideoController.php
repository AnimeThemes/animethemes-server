<?php

declare(strict_types=1);

namespace App\Http\Controllers\Wiki;

use App\Http\Controllers\Controller;
use App\Models\Wiki\Video;
use Illuminate\Support\Facades\Storage;
use Spatie\RouteDiscovery\Attributes\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class VideoController.
 */
class VideoController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // route discovery wants class strings
        $this->middleware(['is_video_streaming_allowed', 'without_trashed:video', 'record_view:video'], ['only' => 'show']);
    }

    /**
     * Stream video.
     *
     * @param  Video  $video
     * @return StreamedResponse
     */
    #[Route(fullUri: 'video/{video}', name: 'video.show')]
    public function show(Video $video): StreamedResponse
    {
        $response = new StreamedResponse();

        $disposition = $response->headers->makeDisposition('inline', basename($video->path));

        $response->headers->replace([
            'Accept-Ranges' => 'bytes',
            'Content-Type' => $video->mimetype,
            'Content-Length' => $video->size,
            'Content-Disposition' => $disposition,
        ]);

        $fs = Storage::disk('videos');

        $response->setCallback(function () use ($fs, $video) {
            $stream = $fs->readStream($video->path);
            if ($stream !== null) {
                fpassthru($stream);
                fclose($stream);
            }
        });

        return $response;
    }
}
