<?php

namespace App\Http\Controllers\Sitemap;

use App\Http\Controllers\Controller;

class EventSitemapController extends Controller
{
    /**
     * Display the event sitemap.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return response()
            ->view('sitemap.event')
            ->header('Content-Type', 'text/xml');
    }
}
