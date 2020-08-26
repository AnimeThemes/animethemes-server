<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

abstract class BaseController extends Controller
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="AnimeThemes.moe Api Documentation",
     *      description="AnimeThemes.moe Laravel RESTful API Documentation",
     * )
     *
     * @OA\Tag(
     *     name="Anime",
     *     description="API Endpoints of Anime"
     * )
     *
     * @OA\Tag(
     *     name="Artist",
     *     description="API Endpoints of Artists"
     * )
     *
     * @OA\Tag(
     *     name="Entry",
     *     description="API Endpoints of Entries"
     * )
     *
     * @OA\Tag(
     *     name="Resource",
     *     description="API Endpoints of Resources"
     * )
     *
     * @OA\Tag(
     *     name="Series",
     *     description="API Endpoints of Series"
     * )
     *
     * @OA\Tag(
     *     name="Song",
     *     description="API Endpoints of Songs"
     * )
     *
     * @OA\Tag(
     *     name="Synonym",
     *     description="API Endpoints of Synonyms"
     * )
     *
     * @OA\Tag(
     *     name="Theme",
     *     description="API Endpoints of Themes"
     * )
     *
     * @OA\Tag(
     *     name="Video",
     *     description="API Endpoints of Videos"
     * )
     */

     /**
      * Get the number of resources to return per page.
      * Acceptable range is [1-100]. Default is 100.
      *
      * @return integer
      */
    protected function getPerPageLimit() : int {
        $limit = intval(request('limit', 100));
        if ($limit <= 0 || $limit > 100) {
            $limit = 100;
        }
        return $limit;
    }
}
