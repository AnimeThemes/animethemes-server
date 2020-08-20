<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EntryCollection;
use App\Http\Resources\EntryResource;
use App\Models\Entry;

class EntryController extends Controller
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
        return new EntryCollection(Entry::with('anime', 'theme', 'videos')->paginate());
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
