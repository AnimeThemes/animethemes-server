<?php

declare(strict_types=1);

namespace App\Http\Controllers\Sitemap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Class CommunitySitemapController.
 */
class CommunitySitemapController extends Controller
{
    /**
     * Display the guideline sitemap.
     *
     * @return Response
     */
    public function show(): Response
    {
        return response()
            ->view('sitemap.community')
            ->header('Content-Type', 'text/xml');
    }
}
