<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Wiki\Resource\SearchResource;
use Illuminate\Http\JsonResponse;

/**
 * Class SearchController.
 */
class SearchController extends BaseController
{
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
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $resource = SearchResource::make($this->parser);

        return $resource->toResponse(request());
    }
}
