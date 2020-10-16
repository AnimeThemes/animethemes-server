<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\EntryCollection;
use App\Http\Resources\EntryResource;
use App\Models\Entry;
use Illuminate\Support\Str;

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
        // initialize builder
        $entries = $this->parser->hasSearch() ? Entry::search($this->parser->getSearch()) : Entry::query();

        // eager load relations
        $entries = $entries->with($this->parser->getIncludePaths(Entry::$allowedIncludePaths));

        // apply filters
        if ($this->parser->hasFilter(static::VERSION_QUERY)) {
            $entries = $entries->whereIn(static::VERSION_QUERY, $this->parser->getFilter(static::VERSION_QUERY));
        }
        if ($this->parser->hasFilter(static::NSFW_QUERY)) {
            $entries = $entries->whereIn(static::NSFW_QUERY, $this->parser->getBooleanFilter(static::NSFW_QUERY));
        }
        if ($this->parser->hasFilter(static::SPOILER_QUERY)) {
            $entries = $entries->whereIn(static::SPOILER_QUERY, $this->parser->getBooleanFilter(static::SPOILER_QUERY));
        }

        // apply sorts
        foreach ($this->parser->getSorts() as $field => $isAsc) {
            if (in_array(Str::lower($field), Entry::$allowedSortFields)) {
                $entries = $entries->orderBy(Str::lower($field), $isAsc ? 'asc' : 'desc');
            }
        }

        // paginate
        $entries = $entries->paginate($this->parser->getPerPageLimit());

        $collection = EntryCollection::make($entries, $this->parser);

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
        $resource = EntryResource::make($entry->load($this->parser->getIncludePaths(Entry::$allowedIncludePaths)), $this->parser);

        return $resource->toResponse(request());
    }
}
