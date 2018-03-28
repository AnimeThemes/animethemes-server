<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideosController extends Controller
{

    public function index() {
        $videos = Video::orderByRaw('udf_NaturalSortFormat(filename, 10, ".")')->paginate(50);

        return view('videos', [
            'videos' => $videos
        ]);
    }

    public function show($alias) {
        set_time_limit(0);

        $video = Video::where('basename', $alias)->orWhere('filename', $alias)->firstOrFail();

        return Storage::disk('spaces')->response($video->path, null, ['Accept-Ranges' => 'bytes']);
    }
}