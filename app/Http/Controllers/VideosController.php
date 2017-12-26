<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideosController extends Controller
{

    public function index() {
        $videos = Video::orderByRaw('udf_NaturalSortFormat(alias, 10, ".")')->paginate(50);

        return view('videos', [
            'videos' => $videos
        ]);
    }

    // Source: https://stackoverflow.com/q/36778167
    public function show($alias) {
        set_time_limit(0);

        $video = Video::where('alias', $alias)->firstOrFail();

        $fs = Storage::disk('spaces')->getDriver();

        $metaData = $fs->getMetadata($video->path);
        $stream = $fs->readStream($video->path);

        if (ob_get_level()) {
            ob_end_clean();
        }

        $headers = [
            'Content-Type' => $metaData['type'],
            'Accept-Ranges' => 'bytes',
            'Content-Length' => $metaData['size'],
            'Content-Range' => 'bytes 0-' . ($metaData['size'] - 1) . '/' . $metaData['size'],
        ];

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
        }, 206, $headers);
    }
}
