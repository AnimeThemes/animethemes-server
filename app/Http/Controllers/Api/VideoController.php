<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoCollection;
use App\Http\Resources\VideoResource;
use App\Models\Video;

class VideoController extends Controller
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
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(@OA\Items(ref="#/components/schemas/VideoResource"))
     *     )
     * )
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new VideoCollection(Video::with('entries', 'entries.theme', 'entries.theme.anime')->paginate());
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
