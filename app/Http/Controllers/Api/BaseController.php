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
use App\JsonApi\FieldSetFilter;
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
use Neomerx\JsonApi\Http\Query\BaseQueryParser;

class BaseController extends Controller
{
    // constants for common query parameters
    protected const SEARCH_QUERY = 'q';
    protected const ORDER_QUERY = 'order';
    protected const DIRECTION_QUERY = 'direction';
    protected const FIELDS_QUERY = 'fields';
    protected const LIMIT_QUERY = 'limit';

    /**
     * Resolves include paths and field sets.
     *
     * @var \Neomerx\JsonApi\Http\Query\BaseQueryParser
     */
    protected $parser;

    public function __construct()
    {
        $this->parser = new BaseQueryParser(request()->all());
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
        $search_query = strval(request(static::SEARCH_QUERY));
        $fields_query = strval(request(static::FIELDS_QUERY));
        $fields = array_filter(explode(',', $fields_query));

        return new JsonResponse([
            AnimeCollection::$wrap => new AnimeCollection(static::excludeResource($search_query, $fields, ArtistCollection::$wrap) ? [] : Anime::search($search_query)
                ->with(AnimeController::getAllowedIncludePaths())
                ->take($this->getPerPageLimit(5))->get(),
                $this->getFieldSets()),
            ArtistCollection::$wrap => new ArtistCollection(static::excludeResource($search_query, $fields, ArtistCollection::$wrap) ? [] : Artist::search($search_query)
                ->with(ArtistController::getAllowedIncludePaths())
                ->take($this->getPerPageLimit(5))->get(),
                $this->getFieldSets()),
            EntryCollection::$wrap => new EntryCollection(static::excludeResource($search_query, $fields, EntryCollection::$wrap) ? [] : Entry::search($search_query)
                ->with(EntryController::getAllowedIncludePaths())
                ->take($this->getPerPageLimit(5))->get(),
                $this->getFieldSets()),
            SeriesCollection::$wrap => new SeriesCollection(static::excludeResource($search_query, $fields, SeriesCollection::$wrap) ? [] : Series::search($search_query)
                ->with(SeriesController::getAllowedIncludePaths())
                ->take($this->getPerPageLimit(5))->get(),
                $this->getFieldSets()),
            SongCollection::$wrap => new SongCollection(static::excludeResource($search_query, $fields, SongCollection::$wrap) ? [] : Song::search($search_query)
                ->with(SongController::getAllowedIncludePaths())
                ->take($this->getPerPageLimit(5))->get(),
                $this->getFieldSets()),
            SynonymCollection::$wrap => new SynonymCollection(static::excludeResource($search_query, $fields, SynonymCollection::$wrap) ? [] : Synonym::search($search_query)
                ->with(SynonymController::getAllowedIncludePaths())
                ->take($this->getPerPageLimit(5))->get(),
                $this->getFieldSets()),
            ThemeCollection::$wrap => new ThemeCollection(static::excludeResource($search_query, $fields, ThemeCollection::$wrap) ? [] : Theme::search($search_query)
                ->with(ThemeController::getAllowedIncludePaths())
                ->take($this->getPerPageLimit(5))->get(),
                $this->getFieldSets()),
            VideoCollection::$wrap => new VideoCollection(static::excludeResource($search_query, $fields, VideoCollection::$wrap) ? [] : Video::search($search_query)
                ->with(VideoController::getAllowedIncludePaths())
                ->take($this->getPerPageLimit(5))->get(),
                $this->getFieldSets()),
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
     * Get the number of resources to return per page.
     * Acceptable range is [1-100]. Default is 100.
     *
     * @param  int  $limit
     * @return int
     */
    protected function getPerPageLimit($limit = 100)
    {
        $limit_query = intval(request(static::LIMIT_QUERY, $limit));
        if ($limit_query <= 0 || $limit_query > $limit) {
            $limit_query = $limit;
        }

        return $limit_query;
    }

    /**
     * Apply ordering to resource query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Laravel\Scout\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|\Laravel\Scout\Builder modified builder
     */
    protected function applyOrdering($query)
    {
        $order_query = Str::lower(request(static::ORDER_QUERY));
        $direction_query = Str::lower(request(static::DIRECTION_QUERY));

        if (! empty($order_query)) {
            if (! empty($direction_query)) {
                return $query->orderBy($order_query, $direction_query);
            } else {
                return $query->orderBy($order_query);
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
     * The validated include paths used to eager load relations.
     *
     * @return array
     */
    protected function getIncludePaths()
    {
        $includePaths = array_keys(iterator_to_array($this->parser->getIncludes()));
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

    /**
     * Get sparse field set filter that will be used by resources for this request.
     *
     * @return \App\JsonApi\FieldSetFilter
     */
    protected function getFieldSets()
    {
        return new FieldSetFilter(iterator_to_array($this->parser->getFields()));
    }
}
