<?php

namespace App\Http\Controllers\Sitemap;

use App\Http\Controllers\Controller;

class SitemapController extends Controller
{
    /**
     * Display the sitemap index.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return response()
            ->view('sitemap.index')
            ->header('Content-Type', 'text/xml');
    }
}
