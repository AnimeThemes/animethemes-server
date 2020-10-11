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
     *         example=1080,
     *         name="resolution",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="Filter videos by NC. Case-insensitive options for true are 1, true, on & yes. Case-insensitive options for false are 0, false, off & no.",
     *         example="false",
     *         name="nc",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         description="Filter videos by subbed. Case-insensitive options for true are 1, true, on & yes. Case-insensitive options for false are 0, false, off & no.",
     *         example="false",
     *         name="subbed",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         description="Filter videos by lyrics. Case-insensitive options for true are 1, true, on & yes. Case-insensitive options for false are 0, false, off & no.",
     *         example="false",
     *         name="lyrics",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         description="Filter videos by uncen. Case-insensitive options for true are 1, true, on & yes. Case-insensitive options for false are 0, false, off & no.",
     *         example="false",
     *         name="uncen",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         description="Filter videos by source. Case-insensitive options are WEB, RAW, BD, DVD & VHS.",
     *         example="BD",
     *         name="source",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Filter videos by overlap. Case-insensitive options are NONE, TRANS & OVER.",
     *         example="None",
     *         name="overlap",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Order videos by field. Case-insensitive options are video_id, created_at, updated_at, filename, path, basename, resolution, nc, subbed, lyrics, uncen, source & overlap.",
     *         example="updated_at",
     *         name="order",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Direction of video ordering. Case-insensitive options are asc & desc.",
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
        // query parameters
        $search_query = strval(request(static::SEARCH_QUERY));
        $resolution_query = strval(request(static::RESOLUTION_QUERY));
        $nc_query = strval(request(static::NC_QUERY));
        $subbed_query = strval(request(static::SUBBED_QUERY));
        $lyrics_query = strval(request(static::LYRICS_QUERY));
        $uncen_query = strval(request(static::UNCEN_QUERY));
        $source_query = Str::upper(request(static::SOURCE_QUERY));
        $overlap_query = Str::upper(request(static::OVERLAP_QUERY));

        // initialize builder
        $videos = empty($search_query) ? Video::query() : Video::search($search_query);

        // eager load relations
        $videos = $videos->with($this->getIncludePaths());

        // apply filters
        if (! empty($resolution_query)) {
            $videos = $videos->where(static::RESOLUTION_QUERY, intval($resolution_query));
        }
        if (! empty($nc_query)) {
            $videos = $videos->where(static::NC_QUERY, filter_var($nc_query, FILTER_VALIDATE_BOOLEAN));
        }
        if (! empty($subbed_query)) {
            $videos = $videos->where(static::SUBBED_QUERY, filter_var($subbed_query, FILTER_VALIDATE_BOOLEAN));
        }
        if (! empty($lyrics_query)) {
            $videos = $videos->where(static::LYRICS_QUERY, filter_var($lyrics_query, FILTER_VALIDATE_BOOLEAN));
        }
        if (! empty($uncen_query)) {
            $videos = $videos->where(static::UNCEN_QUERY, filter_var($uncen_query, FILTER_VALIDATE_BOOLEAN));
        }
        if (! empty($source_query) && SourceType::hasKey($source_query)) {
            $videos = $videos->where(static::SOURCE_QUERY, SourceType::getValue($source_query));
        }
        if (! empty($overlap_query) && OverlapType::hasKey($overlap_query)) {
            $videos = $videos->where(static::OVERLAP_QUERY, OverlapType::getValue($overlap_query));
        }

        // order by
        $videos = $this->applyOrdering($videos);

        // paginate
        $videos = $videos->paginate($this->getPerPageLimit());

        $collection = new VideoCollection($videos, $this->getFieldSets());

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
        $resource = new VideoResource($video->load($this->getIncludePaths()), $this->getFieldSets());

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
}
