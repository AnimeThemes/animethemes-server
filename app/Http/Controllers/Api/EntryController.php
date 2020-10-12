<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\EntryCollection;
use App\Http\Resources\EntryResource;
use App\Models\Entry;

class EntryController extends BaseController
{
    // constants for query parameters
    protected const VERSION_QUERY = 'version';
    protected const NSFW_QUERY = 'nsfw';
    protected const SPOILER_QUERY = 'spoiler';

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
     *         description="The search query. Mapping is to [entry.theme.anime.name|entry.theme.anime.synonyms.text + entry.theme.slug + entry.version] or entry.theme.song.title.",
     *         example="bakemonogatari ED",
     *         name="q",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Comma-separated list of included related resources. Allowed list is anime, themes & videos.",
     *         example="anime,videos",
     *         name="include",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Filter entries by version.",
     *         example=2,
     *         name="version",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="Filter entries by NSFW. Case-insensitive options for true are 1, true, on & yes. Case-insensitive options for false are 0, false, off & no.",
     *         example="false",
     *         name="nsfw",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         description="Filter entries by Spoiler. Case-insensitive options for true are 1, true, on & yes. Case-insensitive options for false are 0, false, off & no.",
     *         example="false",
     *         name="spoiler",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         description="Sort entry resource collection by fields. Case-insensitive options are entry_id, created_at, updated_at, version, nsfw, spoiler & theme_id.",
     *         example="version,-updated_at",
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // query parameters
        $search_query = strval(request(static::SEARCH_QUERY));
        $version_query = strval(request(static::VERSION_QUERY));
        $nsfw_query = strval(request(static::NSFW_QUERY));
        $spoiler_query = strval(request(static::SPOILER_QUERY));

        // initialize builder
        $entries = empty($search_query) ? Entry::query() : Entry::search($search_query);

        // eager load relations
        $entries = $entries->with($this->getIncludePaths());

        // apply filters
        if (! empty($version_query)) {
            $entries = $entries->where(static::VERSION_QUERY, intval($version_query));
        }
        if (! empty($nsfw_query)) {
            $entries = $entries->where(static::NSFW_QUERY, filter_var($nsfw_query, FILTER_VALIDATE_BOOLEAN));
        }
        if (! empty($spoiler_query)) {
            $entries = $entries->where(static::SPOILER_QUERY, filter_var($spoiler_query, FILTER_VALIDATE_BOOLEAN));
        }

        // sort
        $entries = $this->applySorting($entries);

        // paginate
        $entries = $entries->paginate($this->getPerPageLimit());

        $collection = new EntryCollection($entries, $this->getFieldSets());

        return $collection->toResponse(request());
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
     *         example="anime,videos",
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
     * @param  \App\Models\Entry  $entry
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Entry $entry)
    {
        $resource = new EntryResource($entry->load($this->getIncludePaths()), $this->getFieldSets());

        return $resource->toResponse(request());
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @return array
     */
    public static function getAllowedIncludePaths()
    {
        return [
            'anime',
            'theme',
            'videos',
        ];
    }

    /**
     * The sort field names a client is allowed to request.
     *
     * @return array
     */
    public static function getAllowedSortFields()
    {
        return [
            'entry_id',
            'created_at',
            'updated_at',
            'version',
            'nsfw',
            'spoiler',
            'theme_id',
        ];
    }
}
