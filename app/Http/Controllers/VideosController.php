<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VideosController extends Controller
{

    public function index() {
        LOG::info('Page Visit - Video Index');

        $videos = Video::orderByRaw('udf_NaturalSortFormat(alias, 10, ".")')->paginate(50);

        return view('videos', [
            'videos' => $videos
        ]);
    }

    public function show($alias) {
        LOG::info('Page Visit - Video', ['alias' => $alias]);

        set_time_limit(0);

        $video = Video::where('alias', $alias)->firstOrFail();

        return Storage::disk('spaces')->response($video->path);
    }
}