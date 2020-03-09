<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class VideosController extends Controller
{
    public function index() {
        return Redirect::away('https://old.reddit.com/r/AnimeThemes/wiki/index');
    }

    public function show($alias) {
        set_time_limit(0);

        $video = Video::where('basename', $alias)->orWhere('filename', $alias)->firstOrFail();

        return Storage::disk('spaces')->response($video->path, null, ['Accept-Ranges' => 'bytes']);
    }
}
