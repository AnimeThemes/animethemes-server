<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnimeCollection;
use App\Http\Resources\ArtistCollection;
use App\Http\Resources\EntryCollection;
use App\Http\Resources\SeriesCollection;
use App\Http\Resources\SongCollection;
use App\Http\Resources\SynonymCollection;
use App\Http\Resources\ThemeCollection;
use App\Http\Resources\VideoCollection;
use App\JsonApi\QueryParser;
use App\Models\Anime;
use App\Models\Artist;
use App\Models\Entry;
use App\Models\Series;
use App\Models\Song;
use App\Models\Synonym;
use App\Models\Theme;
use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class BaseController extends Controller
{
    /**
     * Resolves include paths and field sets.
     *
     * @var \App\JsonApi\QueryParser
     */
    protected $parser;

    public function __construct()
    {
        $this->parser = new QueryParser(request()->all());
    }

    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="AnimeThemes.moe Api Documentation",
     *      description="AnimeThemes.moe Laravel RESTful API Documentation",
     * )
     *
     * @OA\Tag(
     *     name="General",
     *     description="API Endpoints not targeted to a specific resource"
     * )
     *
     * @OA\Tag(
     *     name="Anime",
     *     description="API Endpoints of Anime"
     * )
     *
     * @OA\Tag(
     *     name="Artist",
     *     description="API Endpoints of Artists"
     * )
     *
     * @OA\Tag(
     *     name="Entry",
     *     description="API Endpoints of Entries"
     * )
     *
     * @OA\Tag(
     *     name="Resource",
     *     description="API Endpoints of Resources"
     * )
     *
     * @OA\Tag(
     *     name="Series",
     *     description="API Endpoints of Series"
     * )
     *
     * @OA\Tag(
     *     name="Song",
     *     description="API Endpoints of Songs"
     * )
     *
     * @OA\Tag(
     *     name="Synonym",
     *     description="API Endpoints of Synonyms"
     * )
     *
     * @OA\Tag(
     *     name="Theme",
     *     description="API Endpoints of Themes"
     * )
     *
     * @OA\Tag(
     *     name="Video",
     *     description="API Endpoints of Videos"
     * )
     */

    /**
     * Search resources.
     *
     * @OA\Get(
     *     path="/search",
     *     operationId="search",
     *     tags={"General"},
     *     summary="Get relevant resources by search criteria",
     *     description="Returns relevant resources by search criteria",
     *     @OA\Parameter(
     *         description="The search query. Mappings are identical to resource searching.",
     *         example="bakemonogatari",
     *         name="q",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="The number of each resource to return. Acceptable range is [1-5]. Default value is 5.",
     *         example=1,
     *         name="limit",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="The comma-separated list of resources to include: anime, artists, entries, series, songs, synonyms, themes, videos.",
     *         example="anime,artists,videos",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="anime",type="array", @OA\Items(ref="#/components/schemas/AnimeResource")),
     *             @OA\Property(property="artists",type="array", @OA\Items(ref="#/components/schemas/ArtistResource")),
     *             @OA\Property(property="entries",type="array", @OA\Items(ref="#/components/schemas/EntryResource")),
     *             @OA\Property(property="series",type="array", @OA\Items(ref="#/components/schemas/SeriesResource")),
     *             @OA\Property(property="songs",type="array", @OA\Items(ref="#/components/schemas/SongResource")),
     *             @OA\Property(property="synonyms",type="array", @OA\Items(ref="#/components/schemas/SynonymResource")),
     *             @OA\Property(property="themes",type="array", @OA\Items(ref="#/components/schemas/ThemeResource")),
     *             @OA\Property(property="videos",type="array", @OA\Items(ref="#/components/schemas/VideoResource"))
     *         )
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search()
    {
        $search_query = $this->parser->getSearch();
        $includes = $this->parser->getIncludes();

        return new JsonResponse([
            AnimeCollection::$wrap => new AnimeCollection(static::excludeResource($search_query, $includes, ArtistCollection::$wrap) ? [] : Anime::search($search_query)
                ->with(AnimeController::getAllowedIncludePaths())
                ->take($this->parser->getPerPageLimit(5))->get(),
                $this->parser),
            ArtistCollection::$wrap => new ArtistCollection(static::excludeResource($search_query, $includes, ArtistCollection::$wrap) ? [] : Artist::search($search_query)
                ->with(ArtistController::getAllowedIncludePaths())
                ->take($this->parser->getPerPageLimit(5))->get(),
                $this->parser),
            EntryCollection::$wrap => new EntryCollection(static::excludeResource($search_query, $includes, EntryCollection::$wrap) ? [] : Entry::search($search_query)
                ->with(EntryController::getAllowedIncludePaths())
                ->take($this->parser->getPerPageLimit(5))->get(),
                $this->parser),
            SeriesCollection::$wrap => new SeriesCollection(static::excludeResource($search_query, $includes, SeriesCollection::$wrap) ? [] : Series::search($search_query)
                ->with(SeriesController::getAllowedIncludePaths())
                ->take($this->parser->getPerPageLimit(5))->get(),
                $this->parser),
            SongCollection::$wrap => new SongCollection(static::excludeResource($search_query, $includes, SongCollection::$wrap) ? [] : Song::search($search_query)
                ->with(SongController::getAllowedIncludePaths())
                ->take($this->parser->getPerPageLimit(5))->get(),
                $this->parser),
            SynonymCollection::$wrap => new SynonymCollection(static::excludeResource($search_query, $includes, SynonymCollection::$wrap) ? [] : Synonym::search($search_query)
                ->with(SynonymController::getAllowedIncludePaths())
                ->take($this->parser->getPerPageLimit(5))->get(),
                $this->parser),
            ThemeCollection::$wrap => new ThemeCollection(static::excludeResource($search_query, $includes, ThemeCollection::$wrap) ? [] : Theme::search($search_query)
                ->with(ThemeController::getAllowedIncludePaths())
                ->take($this->parser->getPerPageLimit(5))->get(),
                $this->parser),
            VideoCollection::$wrap => new VideoCollection(static::excludeResource($search_query, $includes, VideoCollection::$wrap) ? [] : Video::search($search_query)
                ->with(VideoController::getAllowedIncludePaths())
                ->take($this->parser->getPerPageLimit(5))->get(),
                $this->parser),
        ]);
    }

    /**
     * Only perform a search on the resource if there is a search query and the resource type is not excluded in field selection.
     *
     * @param string $search_query the search query
     * @param array $fields the list of resources to include
     * @param string $wrap the resource type identifier
     * @return bool false if we have a search query and the resource is not excluded in field selection, otherwise true
     */
    private static function excludeResource($search_query, $fields, $wrap)
    {
        return empty($search_query) || (! empty($fields) && ! in_array($wrap, $fields));
    }

    /**
     * Apply sorts to resource collections according to one or more sort fields.
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Laravel\Scout\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|\Laravel\Scout\Builder modified builder
     */
    protected function applySorting($query)
    {
        $sorts = $this->parser->getSorts();

        foreach ($sorts as $field => $isAsc) {
            if (in_array(Str::lower($field), static::getAllowedSortFields())) {
                $query = $query->orderBy($field, $isAsc ? 'asc' : 'desc');
            }
        }

        return $query;
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @return array
     */
    public static function getAllowedIncludePaths()
    {
        return [];
    }

    /**
     * The sort field names a client is allowed to request.
     *
     * @return array
     */
    public static function getAllowedSortFields()
    {
        return [];
    }

    /**
     * The validated include paths used to eager load relations.
     *
     * @return array
     */
    protected function getIncludePaths()
    {
        $includePaths = $this->parser->getIncludes();
        $allowedIncludePaths = static::getAllowedIncludePaths();

        // If include paths are not specified, return full list of allowed include paths
        if (empty($includePaths)) {
            return $allowedIncludePaths;
        }

        // If no include paths are contained in the list of allowed include paths,
        // return the full list of allowed include paths
        $validIncludePaths = array_intersect($includePaths, $allowedIncludePaths);
        if (empty($validIncludePaths)) {
            return $allowedIncludePaths;
        }

        // Return list of include paths that are contained in the list of allowed include paths
        return $validIncludePaths;
    }
}
