<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\JsonApi\QueryParser;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="AnimeThemes.moe Api Documentation",
 *      description="AnimeThemes.moe Laravel RESTful API Documentation",
 * )
 *
 * @OA\Tag(
 *     name="General",
 *     description="API Endpoints not targeted to a specific resource"
 * )
 *
 * @OA\Tag(
 *     name="Anime",
 *     description="API Endpoints of Anime"
 * )
 *
 * @OA\Tag(
 *     name="Announcement",
 *     description="API Endpoints of Announcements"
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
 *     name="Image",
 *     description="API Endpoints of Images"
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
abstract class BaseController extends Controller
{
    /**
     * Resolves include paths and field sets.
     *
     * @var \App\JsonApi\QueryParser
     */
    protected $parser;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->parser = new QueryParser(request()->all());
    }
}
