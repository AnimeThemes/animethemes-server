<?php

namespace App\Http\Controllers\Sitemap;

use App\Http\Controllers\Controller;

class CommunitySitemapController extends Controller
{
    /**
     * Display the guideline sitemap.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return response()
            ->view('sitemap.community')
            ->header('Content-Type', 'text/xml');
    }
}
