<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\EntryCollection;
use App\Http\Resources\EntryResource;
use App\Models\Entry;

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
     *         description="The search query. Mapping is to [entry.theme.anime.name|entry.theme.anime.synonyms.text + entry.theme.slug + entry.version] or entry.theme.song.title.",
     *         example="bakemonogatari ED",
     *         name="q",
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
     *         description="The number of resources to return per page. Acceptable range is [1-100]. Default value is 100.",
     *         example=50,
     *         name="limit",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="The comma-separated list of fields to include by dot notation. Wildcards are supported. If unset, all fields are included.",
     *         example="entries.\*.version,\*.link",
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entries = [];

        // query parameters
        $search_query = strval(request('q'));
        $version_query = strval(request('version'));
        $nsfw_query = strval(request('nsfw'));
        $spoiler_query = strval(request('spoiler'));

        if (!empty($search_query)) {
            $entries = Entry::search($search_query)
                ->with(['anime', 'theme', 'videos']);
        } else {
            $entries = Entry::with('anime', 'theme', 'videos');
        }

        // apply filters
        if (!empty($version_query)) {
            $entries = $entries->where('sequence', intval($version_query));
        }
        if (!empty($nsfw_query)) {
            $entries = $entries->where('nsfw', filter_var($nsfw_query, FILTER_VALIDATE_BOOLEAN));
        }
        if (!empty($spoiler_query)) {
            $entries = $entries->where('spoiler', filter_var($spoiler_query, FILTER_VALIDATE_BOOLEAN));
        }

        // paginate
        $entries = $entries->paginate($this->getPerPageLimit());

        return new EntryCollection($entries);
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
     *         description="The comma-separated list of fields to include by dot notation. Wildcards are supported. If unset, all fields are included.",
     *         example="version,\*.link",
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
     * @return \Illuminate\Http\Response
     */
    public function show(Entry $entry)
    {
        return new EntryResource($entry->load('anime', 'theme', 'videos'));
    }
}
