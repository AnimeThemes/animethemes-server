<?php

namespace App\Http\Controllers;

use App\Models\Video;

class SitemapController extends Controller
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $video = Video::orderBy('updated_at', 'desc')->first();

        return response()->view('sitemap.index', [
            'video' => $video,
        ])->header('Content-Type', 'text/xml');
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function videos()
    {
        $video = Video::orderBy('updated_at', 'desc')->first();

        return response()->view('sitemap.videos', [
            'video' => $video,
        ])->header('Content-Type', 'text/xml');
    }
}
