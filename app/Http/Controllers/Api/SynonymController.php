<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SynonymCollection;
use App\Http\Resources\SynonymResource;
use App\Models\Synonym;

class SynonymController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/synonym/",
     *     operationId="getSynonyms",
     *     tags={"Synonym"},
     *     summary="Get paginated listing of Synonyms",
     *     description="Returns listing of Synonyms",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(@OA\Items(ref="#/components/schemas/SynonymResource"))
     *     )
     * )
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new SynonymCollection(Synonym::with('anime')->paginate());
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/synonym/{id}",
     *     operationId="getSynonym",
     *     tags={"Synonym"},
     *     summary="Get properties of Synonym",
     *     description="Returns properties of Synonym",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(ref="#/components/schemas/SynonymResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource Not Found!"
     *     )
     * )
     *
     * @param  \App\Models\Synonym  $synonym
     * @return \Illuminate\Http\Response
     */
    public function show(Synonym $synonym)
    {
        return new SynonymResource($synonym->load('anime'));
    }
}
