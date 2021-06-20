<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Wiki\Collection\ExternalResourceCollection;
use App\Http\Resources\Wiki\Resource\ExternalResourceResource;
use App\Models\Wiki\ExternalResource;
use Illuminate\Http\JsonResponse;

/**
 * Class ExternalResourceController.
 */
class ExternalResourceController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/resource/",
     *     operationId="getResources",
     *     tags={"Resource"},
     *     summary="Get paginated listing of Resources",
     *     description="Returns listing of Resources",
     *     @OA\Parameter(
     *         description="Comma-separated list of included related resources. Allowed list is anime & artists.",
     *         example="include=anime",
     *         name="include",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Filter resources by site. Case-insensitive options are OFFICIAL_SITE, TWITTER, ANIDB, ANILIST, ANIME_PLANET, ANN, KITSU, MAL & WIKI.",
     *         example="filter[site]=MAL",
     *         name="site",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Sort external resource collection by fields. Case-insensitive options are resource_id, created_at, updated_at, site, link & external_id.",
     *         example="sort=-updated_at,resource_id",
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
     *         example="fields[resource]=link,external_id",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(@OA\Property(property="resources",type="array", @OA\Items(ref="#/components/schemas/ExternalResourceResource")))
     *     )
     * )
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $resources = ExternalResourceCollection::performQuery($this->parser);

        return $resources->toResponse(request());
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/resource/{id}",
     *     operationId="getResource",
     *     tags={"Resource"},
     *     summary="Get properties of Resource",
     *     description="Returns properties of Resource",
     *     @OA\Parameter(
     *         description="Comma-separated list of included related resources. Allowed list is anime & artists.",
     *         example="include=anime",
     *         name="include",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="The comma-separated list of fields by resource type",
     *         example="fields[resource]=link,external_id",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(ref="#/components/schemas/ExternalResourceResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource Not Found!"
     *     )
     * )
     *
     * @param ExternalResource $resource
     * @return JsonResponse
     */
    public function show(ExternalResource $resource): JsonResponse
    {
        $resource = ExternalResourceResource::performQuery($resource, $this->parser);

        return $resource->toResponse(request());
    }
}
