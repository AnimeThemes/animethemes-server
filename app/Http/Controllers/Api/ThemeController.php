<?php

namespace App\Http\Controllers\Api;

use App\Enums\ThemeType;
use App\Http\Resources\ThemeCollection;
use App\Http\Resources\ThemeResource;
use App\Models\Theme;

class ThemeController extends BaseController
{
    // constants for query parameters
    protected const TYPE_QUERY = 'type';
    protected const SEQUENCE_QUERY = 'sequence';
    protected const GROUP_QUERY = 'group';

    /**
     * The array of eager relations.
     *
     * @var array
     */
    protected const EAGER_RELATIONS = [
        'anime',
        'entries',
        'entries.videos',
        'song',
        'song.artists'
    ];

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/theme/",
     *     operationId="getThemes",
     *     tags={"Theme"},
     *     summary="Get paginated listing of Themes",
     *     description="Returns listing of Themes",
     *     @OA\Parameter(
     *         description="The search query. Mapping is to [theme.anime.name|theme.anime.synonyms.text + theme.slug] or theme.song.title.",
     *         example="bakemonogatari op1",
     *         name="q",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Filter themes by type. Case-insensitive options are OP & ED.",
     *         example="OP",
     *         name="type",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Filter themes by sequence",
     *         example=1,
     *         name="sequence",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="Filter themes by group.",
     *         example="Dubbed Version",
     *         name="group",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Order themes by field. Case-insensitive options are theme_id, created_at, updated_at, group, type, sequence, slug, anime_id & song_id.",
     *         example="updated_at",
     *         name="order",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Direction of theme ordering. Case-insensitive options are asc & desc.",
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
     *         description="The comma-separated list of fields to include by dot notation. Wildcards are supported. If unset, all fields are included.",
     *         example="themes.\*.sequence,\*.link",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(@OA\Property(property="themes",type="array", @OA\Items(ref="#/components/schemas/ThemeResource")))
     *     )
     * )
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // query parameters
        $search_query = strval(request(static::SEARCH_QUERY));
        $type_query = strval(request(static::TYPE_QUERY));
        $sequence_query = strtoupper(request(static::SEQUENCE_QUERY));
        $group_query = strval(request(static::GROUP_QUERY));

        // initialize builder
        $themes = empty($search_query) ? Theme::query() : Theme::search($search_query);

        // eager load relations
        $themes = $themes->with(static::EAGER_RELATIONS);

        // apply filters
        if (!empty($type_query) && ThemeType::hasKey($type_query)) {
            $themes = $themes->where(static::TYPE_QUERY, ThemeType::getValue($type_query));
        }
        if (!empty($sequence_query)) {
            $themes = $themes->where(static::SEQUENCE_QUERY, intval($sequence_query));
        }
        if (!empty($group_query)) {
            $themes = $themes->where(static::GROUP_QUERY, $group_query);
        }

        // order by
        $themes = $this->applyOrdering($themes);

        // paginate
        $themes = $themes->paginate($this->getPerPageLimit());

        return new ThemeCollection($themes);
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
     *     @OA\Parameter(
     *         description="The comma-separated list of fields to include by dot notation. Wildcards are supported. If unset, all fields are included.",
     *         example="sequence,\*.link",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
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
        return new ThemeResource($theme->load(static::EAGER_RELATIONS));
    }
}
