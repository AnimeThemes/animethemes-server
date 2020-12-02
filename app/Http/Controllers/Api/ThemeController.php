<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ThemeCollection;
use App\Http\Resources\ThemeResource;
use App\Models\Theme;
use Illuminate\Support\Str;

class ThemeController extends BaseController
{
    // constants for query parameters
    public const TYPE_QUERY = 'type';
    public const SEQUENCE_QUERY = 'sequence';
    public const GROUP_QUERY = 'group';

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
     *         description="Comma-separated list of included related resources. Allowed list is anime, anime.images, entries, entries.videos, song & song.artists.",
     *         example="include=anime,song",
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
     *         example="sort=sequence,-updated_at",
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
        // initialize builder with eager loaded relations
        $themes = Theme::with($this->parser->getIncludePaths(Theme::$allowedIncludePaths));

        // apply filters
        $themes = Theme::applyFilters($themes, $this->parser);

        // apply sorts
        foreach ($this->parser->getSorts() as $field => $isAsc) {
            if (in_array(Str::lower($field), Theme::$allowedSortFields)) {
                $themes = $themes->orderBy(Str::lower($field), $isAsc ? 'asc' : 'desc');
            }
        }

        // paginate
        $themes = $themes->jsonPaginate();

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
     *         description="Comma-separated list of included related resources. Allowed list is anime, anime.images, entries, entries.videos, song & song.artists.",
     *         example="include=anime,song",
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
