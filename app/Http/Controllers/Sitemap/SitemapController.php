<?php declare(strict_types=1);

namespace App\Http\Controllers\Sitemap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Class SitemapController
 * @package App\Http\Controllers\Sitemap
 */
class SitemapController extends Controller
{
    /**
     * Display the sitemap index.
     *
     * @return Response
     */
    public function show(): Response
    {
        return response()
            ->view('sitemap.index')
            ->header('Content-Type', 'text/xml');
    }
}
