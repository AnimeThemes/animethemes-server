<?php

namespace App\Http\Controllers\Api;

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
     *         description="Comma-separated list of included related resources. Allowed list is entries, entries.theme & entries.theme.anime.",
     *         example="include=entries",
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
     *         description="Sort video resource collection by fields. Case-insensitive options are video_id, created_at, updated_at, filename, path, size, basename, resolution, nc, subbed, lyrics, uncen, source & overlap.",
     *         example="sort=filename,-updated_at",
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
        $videos = VideoCollection::performQuery($this->parser);

        return $videos->toResponse(request());
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
     *         example="include=entries",
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
        $resource = VideoResource::performQuery($video, $this->parser);

        return $resource->toResponse(request());
    }
}
