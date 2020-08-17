<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExternalResourceResource;
use App\Models\ExternalResource;

class ExternalResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ExternalResource  $externalResource
     * @return \Illuminate\Http\Response
     */
    public function show(ExternalResource $resource)
    {
        return new ExternalResourceResource($resource->load('anime', 'artists'));
    }
}
