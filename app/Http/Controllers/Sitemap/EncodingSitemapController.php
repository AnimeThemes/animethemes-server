<?php

declare(strict_types=1);

namespace App\Http\Controllers\Sitemap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Class EncodingSitemapController
 * @package App\Http\Controllers\Sitemap
 */
class EncodingSitemapController extends Controller
{
    /**
     * Display the encoding sitemap.
     *
     * @return Response
     */
    public function show(): Response
    {
        return response()
            ->view('sitemap.encoding')
            ->header('Content-Type', 'text/xml');
    }
}
