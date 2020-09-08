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
use App\Models\Anime;
use App\Models\Artist;
use App\Models\Entry;
use App\Models\Series;
use App\Models\Song;
use App\Models\Synonym;
use App\Models\Theme;
use App\Models\Video;
use Illuminate\Support\Facades\Schema;

class BaseController extends Controller
{

    // constants for common query parameters
    protected const SEARCH_QUERY = 'q';
    protected const ORDER_QUERY = 'order';
    protected const DIRECTION_QUERY = 'direction';
    protected const FIELDS_QUERY = 'fields';
    protected const LIMIT_QUERY = 'limit';

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
     * Search resources
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
     * @return \Illuminate\Http\Response
     */
    public function search()
    {
        $search_query = strval(request(static::SEARCH_QUERY));
        $fields_query = strval(request(static::FIELDS_QUERY));
        $fields = array_filter(explode(',', $fields_query));

        return [
            AnimeCollection::$wrap => new AnimeCollection(static::excludeResource($search_query, $fields, ArtistCollection::$wrap) ? [] : Anime::search($search_query)
                ->with(AnimeController::EAGER_RELATIONS)
                ->take($this->getPerPageLimit(5))->get()),
            ArtistCollection::$wrap => new ArtistCollection(static::excludeResource($search_query, $fields, ArtistCollection::$wrap) ? [] : Artist::search($search_query)
                ->with(ArtistController::EAGER_RELATIONS)
                ->take($this->getPerPageLimit(5))->get()),
            EntryCollection::$wrap => new EntryCollection(static::excludeResource($search_query, $fields, EntryCollection::$wrap) ? [] : Entry::search($search_query)
                ->with(EntryController::EAGER_RELATIONS)
                ->take($this->getPerPageLimit(5))->get()),
            SeriesCollection::$wrap => new SeriesCollection(static::excludeResource($search_query, $fields, SeriesCollection::$wrap) ? [] : Series::search($search_query)
                ->with(SeriesController::EAGER_RELATIONS)
                ->take($this->getPerPageLimit(5))->get()),
            SongCollection::$wrap => new SongCollection(static::excludeResource($search_query, $fields, SongCollection::$wrap) ? [] : Song::search($search_query)
                ->with(SongController::EAGER_RELATIONS)
                ->take($this->getPerPageLimit(5))->get()),
            SynonymCollection::$wrap => new SynonymCollection(static::excludeResource($search_query, $fields, SynonymCollection::$wrap) ? [] : Synonym::search($search_query)
                ->with(SynonymController::EAGER_RELATIONS)
                ->take($this->getPerPageLimit(5))->get()),
            ThemeCollection::$wrap => new ThemeCollection(static::excludeResource($search_query, $fields, ThemeCollection::$wrap) ? [] : Theme::search($search_query)
                ->with(ThemeController::EAGER_RELATIONS)
                ->take($this->getPerPageLimit(5))->get()),
            VideoCollection::$wrap => new VideoCollection(static::excludeResource($search_query, $fields, VideoCollection::$wrap) ? [] : Video::search($search_query)
                ->with(VideoController::EAGER_RELATIONS)
                ->take($this->getPerPageLimit(5))->get())
        ];
    }

    /**
     * Only perform a search on the resource if there is a search query and the resource type is not excluded in field selection
     *
     * @param string $search_query the search query
     * @param array $fields the list of resources to include
     * @param string $wrap the resource type identifier
     * @return boolean false if we have a search query and the resource is not excluded in field selection, otherwise true
     */
    private static function excludeResource($search_query, $fields, $wrap) : bool {
        return empty($search_query) || (!empty($fields) && !in_array($wrap, $fields));
    }

     /**
      * Get the number of resources to return per page.
      * Acceptable range is [1-100]. Default is 100.
      *
      * @param  integer  $limit
      * @return integer
      */
    protected function getPerPageLimit($limit = 100) : int {
        $limit_query = intval(request(static::LIMIT_QUERY, $limit));
        if ($limit_query <= 0 || $limit_query > $limit) {
            $limit_query = $limit;
        }
        return $limit_query;
    }

    /**
     * Apply ordering to resource query builder
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder modified builder
     */
    protected function applyOrdering($query) {
        $order_query = strtolower(request(static::ORDER_QUERY));
        $direction_query = strtolower(request(static::DIRECTION_QUERY));

        if (!empty($order_query) && Schema::hasColumn($query->getModel()->getTable(), $order_query)) {
            if (!empty($direction_query)) {
                return $query->orderBy($order_query, $direction_query);
            } else {
                return $query->orderBy($order_query);
            }
        }

        return $query;
    }
}
