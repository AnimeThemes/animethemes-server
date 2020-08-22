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
        return new VideoCollection(Video::with('entries', 'entries.theme', 'entries.theme.anime')->paginate($this->getPerPageLimit()));
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
