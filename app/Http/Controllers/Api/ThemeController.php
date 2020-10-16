<?php

namespace App\Http\Controllers\Api;

use App\Enums\ThemeType;
use App\Http\Resources\ThemeCollection;
use App\Http\Resources\ThemeResource;
use App\Models\Theme;
use Illuminate\Support\Str;

class ThemeController extends BaseController
{
    // constants for query parameters
    protected const TYPE_QUERY = 'type';
    protected const SEQUENCE_QUERY = 'sequence';
    protected const GROUP_QUERY = 'group';

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
     *         description="Comma-separated list of included related resources. Allowed list is anime, entries, entries.videos, song & song.artists.",
     *         example="anime,song",
     *         name="include",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Filter themes by type. Case-insensitive options are OP & ED.",
     *         example="filter[type]=OP",
     *         name="type",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Filter themes by sequence",
     *         example="filter[sequence]=1",
     *         name="sequence",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="Filter themes by group.",
     *         example="filter[group]=Dubbed+Version",
     *         name="group",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Sort theme resource collection by fields. Case-insensitive options are theme_id, created_at, updated_at, group, type, sequence, slug, anime_id & song_id.",
     *         example="sequence,-updated_at",
     *         name="sort",
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
     *         description="The comma-separated list of fields by resource type",
     *         example="fields[theme]=slug",
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // initialize builder
        $themes = $this->parser->hasSearch() ? Theme::search($this->parser->getSearch()) : Theme::query();

        // eager load relations
        $themes = $themes->with($this->parser->getIncludePaths(Theme::$allowedIncludePaths));

        // apply filters
        if ($this->parser->hasFilter(static::TYPE_QUERY)) {
            $themes = $themes->whereIn(static::TYPE_QUERY, $this->parser->getEnumFilter(static::TYPE_QUERY, ThemeType::class));
        }
        if ($this->parser->hasFilter(static::SEQUENCE_QUERY)) {
            $themes = $themes->whereIn(static::SEQUENCE_QUERY, $this->parser->getFilter(static::SEQUENCE_QUERY));
        }
        if ($this->parser->hasFilter(static::GROUP_QUERY)) {
            $themes = $themes->whereIn(static::GROUP_QUERY, $this->parser->getFilter(static::GROUP_QUERY));
        }

        // apply sorts
        foreach ($this->parser->getSorts() as $field => $isAsc) {
            if (in_array(Str::lower($field), Theme::$allowedSortFields)) {
                $themes = $themes->orderBy(Str::lower($field), $isAsc ? 'asc' : 'desc');
            }
        }

        // paginate
        $themes = $themes->paginate($this->parser->getPerPageLimit());

        $collection = ThemeCollection::make($themes, $this->parser);

        return $collection->toResponse(request());
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
     *         description="Comma-separated list of included related resources. Allowed list is anime, entries, entries.videos, song & song.artists.",
     *         example="anime,song",
     *         name="include",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="The comma-separated list of fields by resource type",
     *         example="fields[theme]=slug",
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Theme $theme)
    {
        $resource = ThemeResource::make($theme->load($this->parser->getIncludePaths(Theme::$allowedIncludePaths)), $this->parser);

        return $resource->toResponse(request());
    }
}
