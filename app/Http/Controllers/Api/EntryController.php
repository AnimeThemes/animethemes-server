<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\Http\Api\PaginationStrategy;
use App\Http\Resources\EntryCollection;
use App\Http\Resources\EntryResource;
use App\Models\Entry;
use Illuminate\Http\JsonResponse;

/**
 * Class EntryController.
 */
class EntryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/entry/",
     *     operationId="getEntries",
     *     tags={"Entry"},
     *     summary="Get paginated listing of Entries",
     *     description="Returns listing of Entries",
     *     @OA\Parameter(
     *         description="Comma-separated list of included related resources. Allowed list is anime, themes & videos.",
     *         example="include=anime,videos",
     *         name="include",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Filter entries by version.",
     *         example="filter[version]=2",
     *         name="version",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="Filter entries by NSFW. Case-insensitive options for true are 1, true, on & yes. Case-insensitive options for false are 0, false, off & no.",
     *         example="filter[nsfw]=false",
     *         name="nsfw",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         description="Filter entries by Spoiler. Case-insensitive options for true are 1, true, on & yes. Case-insensitive options for false are 0, false, off & no.",
     *         example="filter[spoiler]=false",
     *         name="spoiler",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         description="Sort entry resource collection by fields. Case-insensitive options are entry_id, created_at, updated_at, version, nsfw, spoiler & theme_id.",
     *         example="sort=version,-updated_at",
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
     *         example="fields[entry]=version",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(@OA\Property(property="entries",type="array", @OA\Items(ref="#/components/schemas/EntryResource")))
     *     )
     * )
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        if ($this->parser->hasSearch()) {
            return EntryCollection::performSearch($this->parser, PaginationStrategy::OFFSET())->toResponse(request());
        }

        return EntryCollection::performQuery($this->parser)->toResponse(request());
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/entry/{id}",
     *     operationId="getEntry",
     *     tags={"Entry"},
     *     summary="Get properties of Entry",
     *     description="Returns properties of Entry",
     *     @OA\Parameter(
     *         description="Comma-separated list of included related resources. Allowed list is anime, themes & videos.",
     *         example="include=anime,videos",
     *         name="include",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="The comma-separated list of fields by resource type",
     *         example="fields[entry]=version",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(ref="#/components/schemas/EntryResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource Not Found!"
     *     )
     * )
     *
     * @param Entry $entry
     * @return JsonResponse
     */
    public function show(Entry $entry): JsonResponse
    {
        $resource = EntryResource::performQuery($entry, $this->parser);

        return $resource->toResponse(request());
    }
}
