<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ThemeCollection;
use App\Http\Resources\ThemeResource;
use App\Models\Theme;

class ThemeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/theme/",
     *     operationId="getThemes",
     *     tags={"Theme"},
     *     summary="Get paginated listing of Themes",
     *     description="Returns listing of Themes",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(@OA\Items(ref="#/components/schemas/ThemeResource"))
     *     )
     * )
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new ThemeCollection(Theme::with('anime', 'entries', 'entries.videos', 'song', 'song.artists')->paginate());
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/theme/{id}",
     *     operationId="getTheme",
     *     tags={"Theme"},
     *     summary="Get properties of Theme",
     *     description="Returns properties of Theme",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(ref="#/components/schemas/ThemeResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource Not Found!"
     *     )
     * )
     *
     * @param  \App\Models\Theme  $theme
     * @return \Illuminate\Http\Response
     */
    public function show(Theme $theme)
    {
        return new ThemeResource($theme->load('anime', 'entries', 'entries.videos', 'song', 'song.artists'));
    }
}
