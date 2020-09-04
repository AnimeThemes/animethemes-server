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

class BaseController extends Controller
{
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
     *     summary="Get top 5 relevant resources by search criteria",
     *     description="Returns top 5 relevant resources by search criteria",
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
        $search_query = strval(request('q'));
        $fields_query = strval(request('fields'));
        $fields = array_filter(explode(',', $fields_query));

        return [
            AnimeCollection::$wrap => new AnimeCollection(empty($search_query) || (!empty($fields) && !in_array(AnimeCollection::$wrap, $fields)) ? [] : Anime::search($search_query)
                ->with(['synonyms', 'series', 'themes', 'themes.entries', 'themes.entries.videos', 'themes.song', 'themes.song.artists', 'externalResources'])
                ->take($this->getPerPageLimit(5))->get()),
            ArtistCollection::$wrap => new ArtistCollection(empty($search_query) || (!empty($fields) && !in_array(ArtistCollection::$wrap, $fields)) ? [] : Artist::search($search_query)
                ->with(['songs', 'songs.themes', 'songs.themes.anime', 'members', 'groups', 'externalResources'])
                ->take($this->getPerPageLimit(5))->get()),
            EntryCollection::$wrap => new EntryCollection(empty($search_query) || (!empty($fields) && !in_array(EntryCollection::$wrap, $fields)) ? [] : Entry::search($search_query)
                ->with(['anime', 'theme', 'videos'])
                ->take($this->getPerPageLimit(5))->get()),
            SeriesCollection::$wrap => new SeriesCollection(empty($search_query) || (!empty($fields) && !in_array(SeriesCollection::$wrap, $fields)) ? [] : Series::search($search_query)
                ->with(['anime', 'anime.synonyms', 'anime.themes', 'anime.themes.entries', 'anime.themes.entries.videos', 'anime.themes.song', 'anime.themes.song.artists', 'anime.externalResources'])
                ->take($this->getPerPageLimit(5))->get()),
            SongCollection::$wrap => new SongCollection(empty($search_query) || (!empty($fields) && !in_array(SongCollection::$wrap, $fields)) ? [] : Song::search($search_query)
                ->with(['themes', 'themes.anime', 'artists'])
                ->take($this->getPerPageLimit(5))->get()),
            SynonymCollection::$wrap => new SynonymCollection(empty($search_query) || (!empty($fields) && !in_array(SynonymCollection::$wrap, $fields)) ? [] : Synonym::search($search_query)
                ->with('anime')
                ->take($this->getPerPageLimit(5))->get()),
            ThemeCollection::$wrap => new ThemeCollection(empty($search_query) || (!empty($fields) && !in_array(ThemeCollection::$wrap, $fields)) ? [] : Theme::search($search_query)
                ->with(['anime', 'entries', 'entries.videos', 'song', 'song.artists'])
                ->take($this->getPerPageLimit(5))->get()),
            VideoCollection::$wrap => new VideoCollection(empty($search_query) || (!empty($fields) && !in_array(VideoCollection::$wrap, $fields)) ? [] : Video::search($search_query)
                ->with(['entries', 'entries.theme', 'entries.theme.anime'])
                ->take($this->getPerPageLimit(5))->get())
        ];
    }

     /**
      * Get the number of resources to return per page.
      * Acceptable range is [1-100]. Default is 100.
      *
      * @param  integer  $limit
      * @return integer
      */
    protected function getPerPageLimit($limit = 100) : int {
        $limit_query = intval(request('limit', $limit));
        if ($limit_query <= 0 || $limit_query > $limit) {
            $limit_query = $limit;
        }
        return $limit_query;
    }
}
