<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExternalResourceCollection;
use App\Http\Resources\ExternalResourceResource;
use App\Models\ExternalResource;

class ExternalResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/resource/",
     *     operationId="getResources",
     *     tags={"Resource"},
     *     summary="Get paginated listing of Resources",
     *     description="Returns listing of Resources",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(@OA\Items(ref="#/components/schemas/ExternalResourceResource"))
     *     )
     * )
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new ExternalResourceCollection(ExternalResource::with('anime', 'artists')->paginate());
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/resource/{id}",
     *     operationId="getResource",
     *     tags={"Resource"},
     *     summary="Get properties of Resource",
     *     description="Returns properties of Resource",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(ref="#/components/schemas/ExternalResourceResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource Not Found!"
     *     )
     * )
     *
     * @param  \App\Models\ExternalResource  $externalResource
     * @return \Illuminate\Http\Response
     */
    public function show(ExternalResource $resource)
    {
        return new ExternalResourceResource($resource->load('anime', 'artists'));
    }
}
