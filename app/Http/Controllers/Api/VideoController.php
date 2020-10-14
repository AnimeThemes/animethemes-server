<?php

namespace App\Http\Controllers\Api;

use App\Enums\OverlapType;
use App\Enums\SourceType;
use App\Http\Resources\VideoCollection;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Support\Str;

class VideoController extends BaseController
{
    // constants for query parameters
    protected const RESOLUTION_QUERY = 'resolution';
    protected const NC_QUERY = 'nc';
    protected const SUBBED_QUERY = 'subbed';
    protected const LYRICS_QUERY = 'lyrics';
    protected const UNCEN_QUERY = 'uncen';
    protected const SOURCE_QUERY = 'source';
    protected const OVERLAP_QUERY = 'overlap';

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/video/",
     *     operationId="getVideos",
     *     tags={"Video"},
     *     summary="Get paginated listing of Videos",
     *     description="Returns listing of Videos",
     *     @OA\Parameter(
     *         description="The search query. Mapping is to video.filename or video.tags[] or [video.entries.theme.anime.name|video.entries.theme.anime.synonyms.text + video.entries.theme.slug + video.entries.version] or video.entries.theme.song.title.",
     *         example="bakemonogatari ED NC BD 1080",
     *         name="q",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Comma-separated list of included related resources. Allowed list is entries, entries.theme & entries.theme.anime.",
     *         example="entries",
     *         name="include",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Filter videos by resolution",
     *         example="filter[resolution]=1080",
     *         name="resolution",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="Filter videos by NC. Case-insensitive options for true are 1, true, on & yes. Case-insensitive options for false are 0, false, off & no.",
     *         example="filter[nc]=false",
     *         name="nc",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         description="Filter videos by subbed. Case-insensitive options for true are 1, true, on & yes. Case-insensitive options for false are 0, false, off & no.",
     *         example="filter[subbed]=false",
     *         name="subbed",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         description="Filter videos by lyrics. Case-insensitive options for true are 1, true, on & yes. Case-insensitive options for false are 0, false, off & no.",
     *         example="filter[lyrics]=false",
     *         name="lyrics",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         description="Filter videos by uncen. Case-insensitive options for true are 1, true, on & yes. Case-insensitive options for false are 0, false, off & no.",
     *         example="filter[uncen]=false",
     *         name="uncen",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         description="Filter videos by source. Case-insensitive options are WEB, RAW, BD, DVD & VHS.",
     *         example="filter[source]=BD",
     *         name="source",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Filter videos by overlap. Case-insensitive options are NONE, TRANS & OVER.",
     *         example="filter[overlap]=None",
     *         name="overlap",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Sort video resource collection by fields. Case-insensitive options are video_id, created_at, updated_at, filename, path, basename, resolution, nc, subbed, lyrics, uncen, source & overlap.",
     *         example="filename,-updated_at",
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
     *         example="fields[video]=basename,link",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(@OA\Property(property="videos",type="array", @OA\Items(ref="#/components/schemas/VideoResource")))
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // initialize builder
        $videos = $this->parser->hasSearch() ? Video::search($this->parser->getSearch()) : Video::query();

        // eager load relations
        $videos = $videos->with($this->getIncludePaths());

        // apply filters
        if ($this->parser->hasFilter(static::RESOLUTION_QUERY)) {
            $videos = $videos->whereIn(static::RESOLUTION_QUERY, $this->parser->getFilter(static::RESOLUTION_QUERY));
        }
        if ($this->parser->hasFilter(static::NC_QUERY)) {
            $videos = $videos->whereIn(static::NC_QUERY, $this->parser->getBooleanFilter(static::NC_QUERY));
        }
        if ($this->parser->hasFilter(static::SUBBED_QUERY)) {
            $videos = $videos->whereIn(static::SUBBED_QUERY, $this->parser->getBooleanFilter(static::SUBBED_QUERY));
        }
        if ($this->parser->hasFilter(static::LYRICS_QUERY)) {
            $videos = $videos->whereIn(static::LYRICS_QUERY, $this->parser->getBooleanFilter(static::LYRICS_QUERY));
        }
        if ($this->parser->hasFilter(static::UNCEN_QUERY)) {
            $videos = $videos->whereIn(static::UNCEN_QUERY, $this->parser->getBooleanFilter(static::UNCEN_QUERY));
        }
        if ($this->parser->hasFilter(static::SOURCE_QUERY)) {
            $videos = $videos->whereIn(static::SOURCE_QUERY, $this->parser->getEnumFilter(static::SOURCE_QUERY, SourceType::class));
        }
        if ($this->parser->hasFilter(static::OVERLAP_QUERY)) {
            $videos = $videos->whereIn(static::OVERLAP_QUERY, $this->parser->getEnumFilter(static::OVERLAP_QUERY, OverlapType::class));
        }

        // sort
        $videos = $this->applySorting($videos);

        // paginate
        $videos = $videos->paginate($this->parser->getPerPageLimit());

        $collection = new VideoCollection($videos, $this->parser);

        return $collection->toResponse(request());
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/video/{basename}",
     *     operationId="getVideo",
     *     tags={"Video"},
     *     summary="Get properties of Video",
     *     description="Returns properties of Video",
     *     @OA\Parameter(
     *         description="Comma-separated list of included related resources. Allowed list is entries, entries.theme & entries.theme.anime.",
     *         example="entries",
     *         name="include",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="The comma-separated list of fields by resource type",
     *         example="fields[video]=basename,link",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(ref="#/components/schemas/VideoResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource Not Found!"
     *     )
     * )
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Video $video)
    {
        $resource = new VideoResource($video->load($this->getIncludePaths()), $this->parser);

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
            'entries',
            'entries.theme',
            'entries.theme.anime',
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
            'video_id',
            'created_at',
            'updated_at',
            'filename',
            'path',
            'basename',
            'resolution',
            'nc',
            'subbed',
            'lyrics',
            'uncen',
            'source',
            'overlap',
        ];
    }
}
