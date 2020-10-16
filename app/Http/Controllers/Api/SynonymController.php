<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SynonymCollection;
use App\Http\Resources\SynonymResource;
use App\Models\Synonym;
use Illuminate\Support\Str;

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
     *         description="The search query. Mapping is to synonym.text.",
     *         example="Monstory",
     *         name="q",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Comma-separated list of included related resources. Allowed list is anime.",
     *         example="anime",
     *         name="include",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Sort synonym resource collection by fields. Case-insensitive options are synonym_id, created_at, updated_at, text & anime_id.",
     *         example="text,-updated_at",
     *         name="sort",
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // initialize builder
        $synonyms = $this->parser->hasSearch() ? Synonym::search($this->parser->getSearch()) : Synonym::query();

        // eager load relations
        $synonyms = $synonyms->with($this->parser->getIncludePaths(Synonym::$allowedIncludePaths));

        // apply sorts
        foreach ($this->parser->getSorts() as $field => $isAsc) {
            if (in_array(Str::lower($field), Synonym::$allowedSortFields)) {
                $synonyms = $synonyms->orderBy(Str::lower($field), $isAsc ? 'asc' : 'desc');
            }
        }

        // paginate
        $synonyms = $synonyms->paginate($this->parser->getPerPageLimit());

        $collection = SynonymCollection::make($synonyms, $this->parser);

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
     *         description="Comma-separated list of included related resources. Allowed list is anime.",
     *         example="anime",
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
     * @param  \App\Models\Synonym  $synonym
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Synonym $synonym)
    {
        $resource = SynonymResource::make($synonym->load($this->parser->getIncludePaths(Synonym::$allowedIncludePaths)), $this->parser);

        return $resource->toResponse(request());
    }
}
