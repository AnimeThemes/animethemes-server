<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SitemapController extends Controller
{
    public function index() {
        LOG::info('Page Visit - Sitemap Index');

        $video = Video::orderBy('updated_at', 'desc')->first();

        return response()->view('sitemap.index', [
            'video' => $video
        ])->header('Content-Type', 'text/xml');
    }

    public function videos() {
        LOG::info('Page Visit - Videos Sitemap');

        $videos = Video::all();
        return response()->view('sitemap.videos', [
            'videos' => $videos
        ])->header('Content-Type', 'text/xml');
    }
}
