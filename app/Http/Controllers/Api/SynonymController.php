<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SynonymCollection;
use App\Http\Resources\SynonymResource;
use App\Models\Synonym;

class SynonymController extends BaseController
{
    /**
     * The array of eager relations.
     *
     * @var array
     */
    protected const EAGER_RELATIONS = [
        'anime',
    ];

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/synonym/",
     *     operationId="getSynonyms",
     *     tags={"Synonym"},
     *     summary="Get paginated listing of Synonyms",
     *     description="Returns listing of Synonyms",
     *     @OA\Parameter(
     *         description="The search query. Mapping is to synonym.text.",
     *         example="Monstory",
     *         name="q",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Order synonyms by field. Case-insensitive options are synonym_id, created_at, updated_at, text & anime_id.",
     *         example="updated_at",
     *         name="order",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Direction of synonym ordering. Case-insensitive options are asc & desc.",
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
     *         example="synonyms.\*.text,\*.name",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(@OA\Property(property="synonyms",type="array", @OA\Items(ref="#/components/schemas/SynonymResource")))
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // query parameters
        $search_query = strval(request(static::SEARCH_QUERY));

        // initialize builder
        $synonyms = empty($search_query) ? Synonym::query() : Synonym::search($search_query);

        // eager load relations
        $synonyms = $synonyms->with(static::EAGER_RELATIONS);

        // order by
        $synonyms = $this->applyOrdering($synonyms);

        // paginate
        $synonyms = $synonyms->paginate($this->getPerPageLimit());

        $collection = new SynonymCollection($synonyms);
        return $collection->toResponse(request());
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/synonym/{id}",
     *     operationId="getSynonym",
     *     tags={"Synonym"},
     *     summary="Get properties of Synonym",
     *     description="Returns properties of Synonym",
     *     @OA\Parameter(
     *         description="The comma-separated list of fields to include by dot notation. Wildcards are supported. If unset, all fields are included.",
     *         example="text,\*.name",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(ref="#/components/schemas/SynonymResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource Not Found!"
     *     )
     * )
     *
     * @param  \App\Models\Synonym  $synonym
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Synonym $synonym)
    {
        $resource = new SynonymResource($synonym->load(static::EAGER_RELATIONS));
        return $resource->toResponse(request());
    }
}
