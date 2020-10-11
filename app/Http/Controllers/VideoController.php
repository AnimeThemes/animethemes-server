<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|void
     */
    public function show(Video $video)
    {
        if (App::environment(['local', 'production'])) {
            set_time_limit(0);

            return Storage::disk('spaces')->response($video->path, null, ['Accept-Ranges' => 'bytes']);
        }
    }
}
