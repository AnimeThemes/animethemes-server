<?php

namespace App\Http\Controllers\Api;

use App\Enums\ResourceSite;
use App\Http\Resources\ExternalResourceCollection;
use App\Http\Resources\ExternalResourceResource;
use App\Models\ExternalResource;
use Illuminate\Support\Str;

class ExternalResourceController extends BaseController
{
    // constants for query parameters
    protected const SITE_QUERY = 'site';

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // initialize builder with eager loaded relations
        $resources = ExternalResource::with($this->parser->getIncludePaths(ExternalResource::$allowedIncludePaths));

        // apply filters
        if ($this->parser->hasFilter(static::SITE_QUERY)) {
            $resources = $resources->whereIn(static::SITE_QUERY, $this->parser->getEnumFilter(static::SITE_QUERY, ResourceSite::class));
        }

        // apply sorts
        foreach ($this->parser->getSorts() as $field => $isAsc) {
            if (in_array(Str::lower($field), ExternalResource::$allowedSortFields)) {
                $resources = $resources->orderBy(Str::lower($field), $isAsc ? 'asc' : 'desc');
            }
        }

        // paginate
        $resources = $resources->jsonPaginate();

        $collection = ExternalResourceCollection::make($resources, $this->parser);

        return $collection->toResponse(request());
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
     * @param  \App\Models\ExternalResource  $resource
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ExternalResource $resource)
    {
        $resource = ExternalResourceResource::make($resource->load($this->parser->getIncludePaths(ExternalResource::$allowedIncludePaths)), $this->parser);

        return $resource->toResponse(request());
    }
}
