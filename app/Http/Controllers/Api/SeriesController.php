<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\Http\Api\PaginationStrategy;
use App\Http\Resources\SeriesCollection;
use App\Http\Resources\SeriesResource;
use App\Models\Series;
use Illuminate\Http\JsonResponse;

/**
 * Class SeriesController.
 */
class SeriesController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/series/",
     *     operationId="getSerie",
     *     tags={"Series"},
     *     summary="Get paginated listing of Series",
     *     description="Returns listing of Series",
     *     @OA\Parameter(
     *         description="Comma-separated list of included related resources. Allowed list is anime.synonyms, anime.themes, anime.themes.entries, anime.themes.entries.videos, anime.themes.song, anime.themes.song.artists, anime.externalResources & anime.images.",
     *         example="include=anime.synonyms,anime.themes",
     *         name="include",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Sort series resource collection by fields. Case-insensitive options are series_id, created_at, updated_at, slug & name.",
     *         example="sort=updated_at",
     *         name="sort",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="The number of resources to return per page. Acceptable range is [1-30]. Default value is 30.",
     *         example="page[size]=25",
     *         name="page[size]",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="The page of resources to return.",
     *         example="page[number]=2",
     *         name="page[number]",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="The comma-separated list of fields by resource type",
     *         example="fields[series]=name,slug",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(@OA\Property(property="series",type="array", @OA\Items(ref="#/components/schemas/SeriesResource")))
     *     )
     * )
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        if ($this->parser->hasSearch()) {
            return SeriesCollection::performSearch($this->parser, PaginationStrategy::OFFSET())->toResponse(request());
        }

        return SeriesCollection::performQuery($this->parser)->toResponse(request());
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/series/{slug}",
     *     operationId="getSeries",
     *     tags={"Series"},
     *     summary="Get properties of Series",
     *     description="Returns properties of Series",
     *     @OA\Parameter(
     *         description="Comma-separated list of included related resources. Allowed list is anime.synonyms, anime.themes, anime.themes.entries, anime.themes.entries.videos, anime.themes.song, anime.themes.song.artists, anime.externalResources & anime.images.",
     *         example="include=anime.synonyms,anime.themes",
     *         name="include",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="The comma-separated list of fields by resource type",
     *         example="fields[series]=name,slug",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(ref="#/components/schemas/SeriesResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource Not Found!"
     *     )
     * )
     *
     * @param Series $series
     * @return JsonResponse
     */
    public function show(Series $series): JsonResponse
    {
        $resource = SeriesResource::performQuery($series, $this->parser);

        return $resource->toResponse(request());
    }
}
