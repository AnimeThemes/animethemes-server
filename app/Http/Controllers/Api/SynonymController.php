<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\JsonApi\PaginationStrategy;
use App\Http\Resources\SynonymCollection;
use App\Http\Resources\SynonymResource;
use App\Models\Synonym;
use Illuminate\Http\JsonResponse;

/**
 * Class SynonymController
 * @package App\Http\Controllers\Api
 */
class SynonymController extends BaseController
{
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
     *         description="Comma-separated list of included related resources. Allowed list is anime.",
     *         example="include=anime",
     *         name="include",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Sort synonym resource collection by fields. Case-insensitive options are synonym_id, created_at, updated_at, text & anime_id.",
     *         example="sort=text,-updated_at",
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
     *         example="fields[synonym]=text",
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
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        if ($this->parser->hasSearch()) {
            return SynonymCollection::performSearch($this->parser, PaginationStrategy::OFFSET())->toResponse(request());
        }

        return SynonymCollection::performQuery($this->parser)->toResponse(request());
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
     *         description="Comma-separated list of included related resources. Allowed list is anime.",
     *         example="include=anime",
     *         name="include",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="The comma-separated list of fields by resource type",
     *         example="fields[synonym]=text",
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
     * @param Synonym $synonym
     * @return JsonResponse
     */
    public function show(Synonym $synonym): JsonResponse
    {
        $resource = SynonymResource::performQuery($synonym, $this->parser);

        return $resource->toResponse(request());
    }
}
