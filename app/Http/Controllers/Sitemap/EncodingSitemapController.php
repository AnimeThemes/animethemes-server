<?php

namespace App\Http\Controllers\Sitemap;

use App\Http\Controllers\Controller;

class EncodingSitemapController extends Controller
{
    /**
     * Display the encoding sitemap.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return response()
            ->view('sitemap.encoding')
            ->header('Content-Type', 'text/xml');
    }
}
