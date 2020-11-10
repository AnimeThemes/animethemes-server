<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SearchResource;
use App\JsonApi\QueryParser;

class BaseController extends Controller
{
    /**
     * Resolves include paths and field sets.
     *
     * @var \App\JsonApi\QueryParser
     */
    protected $parser;

    public function __construct()
    {
        $this->parser = new QueryParser(request()->all());
    }

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
     * Search resources.
     *
     * @OA\Get(
     *     path="/search",
     *     operationId="search",
     *     tags={"General"},
     *     summary="Get relevant resources by search criteria",
     *     description="Returns relevant resources by search criteria",
     *     @OA\Parameter(
     *         description="The search query. Mappings are identical to resource searching.",
     *         example="bakemonogatari",
     *         name="q",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="The number of each resource to return. Acceptable range is [1-5]. Default value is 5.",
     *         example=1,
     *         name="limit",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="The comma-separated list of resources to include: anime, artists, entries, series, songs, synonyms, themes, videos.",
     *         example="anime,artists,videos",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="anime",type="array", @OA\Items(ref="#/components/schemas/AnimeResource")),
     *             @OA\Property(property="artists",type="array", @OA\Items(ref="#/components/schemas/ArtistResource")),
     *             @OA\Property(property="entries",type="array", @OA\Items(ref="#/components/schemas/EntryResource")),
     *             @OA\Property(property="series",type="array", @OA\Items(ref="#/components/schemas/SeriesResource")),
     *             @OA\Property(property="songs",type="array", @OA\Items(ref="#/components/schemas/SongResource")),
     *             @OA\Property(property="synonyms",type="array", @OA\Items(ref="#/components/schemas/SynonymResource")),
     *             @OA\Property(property="themes",type="array", @OA\Items(ref="#/components/schemas/ThemeResource")),
     *             @OA\Property(property="videos",type="array", @OA\Items(ref="#/components/schemas/VideoResource"))
     *         )
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search()
    {
        $resource = SearchResource::make($this->parser);

        return $resource->toResponse(request());
    }
}
