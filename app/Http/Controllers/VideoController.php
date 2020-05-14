<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function index()
    {
        return view('video.index');
    }

    public function show(Video $video)
    {
        set_time_limit(0);
        return Storage::disk('spaces')->response($video->path, null, ['Accept-Ranges' => 'bytes']);
    }
}
