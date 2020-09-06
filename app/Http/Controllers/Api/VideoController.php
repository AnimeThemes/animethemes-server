<?php

namespace App\Http\Controllers\Api;

use App\Enums\OverlapType;
use App\Enums\SourceType;
use App\Http\Resources\VideoCollection;
use App\Http\Resources\VideoResource;
use App\Models\Video;

class VideoController extends BaseController
{
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
     *         description="The number of resources to return per page. Acceptable range is [1-100]. Default value is 100.",
     *         example=50,
     *         name="limit",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="The comma-separated list of fields to include by dot notation. Wildcards are supported. If unset, all fields are included.",
     *         example="videos.\*.link,\*.alias",
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $videos = [];

        // query parameters
        $search_query = strval(request('q'));
        $resolution_query = strval(request('resolution'));
        $nc_query = strval(request('nc'));
        $subbed_query = strval(request('subbed'));
        $lyrics_query = strval(request('lyrics'));
        $uncen_query = strval(request('uncen'));
        $source_query = strtoupper(request('source'));
        $overlap_query = strtoupper(request('overlap'));

        // apply search query
        if (!empty($search_query)) {
            $videos = Video::search($search_query)
                ->with(['entries', 'entries.theme', 'entries.theme.anime']);
        } else {
            $videos = Video::with('entries', 'entries.theme', 'entries.theme.anime');
        }

        // apply filters
        if (!empty($resolution_query)) {
            $videos = $videos->where('resolution', intval($resolution_query));
        }
        if (!empty($nc_query)) {
            $videos = $videos->where('nc', filter_var($nc_query, FILTER_VALIDATE_BOOLEAN));
        }
        if (!empty($subbed_query)) {
            $videos = $videos->where('subbed', filter_var($subbed_query, FILTER_VALIDATE_BOOLEAN));
        }
        if (!empty($lyrics_query)) {
            $videos = $videos->where('lyrics', filter_var($lyrics_query, FILTER_VALIDATE_BOOLEAN));
        }
        if (!empty($uncen_query)) {
            $videos = $videos->where('uncen', filter_var($uncen_query, FILTER_VALIDATE_BOOLEAN));
        }
        if (!empty($source_query)) {
            $videos = $videos->where('source', SourceType::getValue($source_query));
        }
        if (!empty($overlap_query)) {
            $videos = $videos->where('overlap', OverlapType::getValue($overlap_query));
        }

        // paginate
        $videos = $videos->paginate($this->getPerPageLimit());

        return new VideoCollection($videos);
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
     *         description="The comma-separated list of fields to include by dot notation. Wildcards are supported. If unset, all fields are included.",
     *         example="link,\*.alias",
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
     * @return \Illuminate\Http\Response
     */
    public function show(Video $video)
    {
        return new VideoResource($video->load('entries', 'entries.theme', 'entries.theme.anime'));
    }
}
