<?php

declare(strict_types=1);

namespace App\Http\Controllers\Sitemap;

use App\Http\Controllers\Controller;
use App\Models\Document\Page;
use Illuminate\Http\Response;

/**
 * Class PagesSitemapController.
 */
class PagesSitemapController extends Controller
{
    /**
     * Display the sitemap index.
     *
     * @return Response
     */
    public function show(): Response
    {
        return response()
            ->view('sitemap.pages', [
                'pages' => Page::all([Page::ATTRIBUTE_SLUG]),
            ])
            ->header('Content-Type', 'text/xml');
    }
}
