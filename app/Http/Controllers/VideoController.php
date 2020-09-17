<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VideoController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function show(Video $video) : ?StreamedResponse
    {
        if (env('APP_ENV') !== 'staging') {
            set_time_limit(0);
            return Storage::disk('spaces')->response($video->path, null, ['Accept-Ranges' => 'bytes']);
        }

        return null;
    }
}
