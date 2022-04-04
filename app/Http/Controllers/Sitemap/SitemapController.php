<?php

declare(strict_types=1);

namespace App\Http\Controllers\Sitemap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Spatie\RouteDiscovery\Attributes\Route;

/**
 * Class SitemapController.
 */
class SitemapController extends Controller
{
    /**
     * Display the sitemap index.
     *
     * @return Response
     */
    #[Route(fullUri: 'sitemap', name: 'sitemap')]
    public function show(): Response
    {
        return response()
            ->view('sitemap.index')
            ->header('Content-Type', 'text/xml');
    }
}
