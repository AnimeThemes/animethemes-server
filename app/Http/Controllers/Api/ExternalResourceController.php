<?php

namespace App\Http\Controllers\Api;

use App\Enums\ResourceType;
use App\Http\Resources\ExternalResourceCollection;
use App\Http\Resources\ExternalResourceResource;
use App\Models\ExternalResource;

class ExternalResourceController extends BaseController
{
    // constants for query parameters
    protected const TYPE_QUERY = 'type';

    /**
     * The array of eager relations.
     *
     * @var array
     */
    protected const EAGER_RELATIONS = [
        'anime',
        'artists'
    ];

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
     *         description="Filter resources by type. Case-insensitive options are OFFICIAL_SITE, TWITTER, ANIDB, ANILIST, ANIME_PLANET, ANN, KITSU, MAL & WIKI.",
     *         example="MAL",
     *         name="type",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Order resources by field. Case-insensitive options are resource_id, created_at, updated_at, type, link & external_id.",
     *         example="updated_at",
     *         name="order",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Direction of resource ordering. Case-insensitive options are asc & desc.",
     *         example="desc",
     *         name="direction",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="The number of resources to return per page. Acceptable range is [1-100]. Default value is 100.",
     *         example=50,
     *         name="limit",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="The comma-separated list of fields to include by dot notation. Wildcards are supported. If unset, all fields are included.",
     *         example="resources.\*.link,\*.name",
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // query parameters
        $type_query = strtoupper(request(static::TYPE_QUERY));

        // initialize builder
        $resources = ExternalResource::query();

        // eager load relations
        $resources = $resources->with(static::EAGER_RELATIONS);

        // apply filters
        if (!empty($type_query) && ResourceType::hasKey($type_query)) {
            $resources = $resources->where(static::TYPE_QUERY, ResourceType::getValue($type_query));
        }

        // order by
        $resources = $this->applyOrdering($resources);

        // paginate
        $resources = $resources->paginate($this->getPerPageLimit());

        return new ExternalResourceCollection($resources);
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
     *         description="The comma-separated list of fields to include by dot notation. Wildcards are supported. If unset, all fields are included.",
     *         example="link,\*.name",
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
     * @param  \App\Models\ExternalResource  $externalResource
     * @return \Illuminate\Http\Response
     */
    public function show(ExternalResource $resource)
    {
        return new ExternalResourceResource($resource->load(static::EAGER_RELATIONS));
    }
}
