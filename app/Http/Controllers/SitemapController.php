<?php

namespace App\Http\Controllers;

class SitemapController extends Controller
{
    /**
     * Display the sitemap index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()
            ->view('sitemap.index')
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Display the sitemap for encoding-related pages.
     *
     * @return \Illuminate\Http\Response
     */
    public function encoding()
    {
        return response()
            ->view('sitemap.encoding')
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Display the sitemap for guideline-related pages.
     *
     * @return \Illuminate\Http\Response
     */
    public function guidelines()
    {
        return response()
            ->view('sitemap.guidelines')
            ->header('Content-Type', 'text/xml');
    }
}
