<?php

declare(strict_types=1);

namespace App\Http\Controllers\Sitemap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Class EventSitemapController
 * @package App\Http\Controllers\Sitemap
 */
class EventSitemapController extends Controller
{
    /**
     * Display the event sitemap.
     *
     * @return Response
     */
    public function show(): Response
    {
        return response()
            ->view('sitemap.event')
            ->header('Content-Type', 'text/xml');
    }
}
