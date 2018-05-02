<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function index() {
        $video = Video::orderBy('updated_at', 'desc')->first();

        return response()->view('sitemap.index', [
            'video' => $video
        ])->header('Content-Type', 'text/xml');
    }

    public function videos() {
        $video = Video::orderBy('updated_at', 'desc')->first();

        return response()->view('sitemap.videos', [
            'video' => $video
        ])->header('Content-Type', 'text/xml');
    }
}
