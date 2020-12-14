<?php

namespace App\Http\Resources;

use App\Models\Anime;
use App\Models\Artist;
use App\Models\Entry;
use App\Models\Series;
use App\Models\Song;
use App\Models\Synonym;
use App\Models\Theme;
use App\Models\Video;
use Illuminate\Http\Resources\MissingValue;

class SearchResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = null;

    /**
     * The name of the resource in the field set mapping.
     *
     * @var string
     */
    public static $resourceType = 'search';

    /**
     * Create a new resource instance.
     *
     * @param mixed  $parser
     * @return void
     */
    public function __construct($parser)
    {
        parent::__construct(new MissingValue, $parser);
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            AnimeCollection::$wrap => $this->when(
                $this->parser->hasSearch() && $this->isAllowedField(AnimeCollection::$wrap),
                AnimeCollection::make(
                    Anime::search($this->parser->getSearch())
                        ->with($this->parser->getResourceIncludePaths(Anime::$allowedIncludePaths, AnimeResource::$resourceType))
                        ->get(),
                    $this->parser
                )
            ),
            ArtistCollection::$wrap => $this->when(
                $this->parser->hasSearch() && $this->isAllowedField(ArtistCollection::$wrap),
                ArtistCollection::make(
                    Artist::search($this->parser->getSearch())
                        ->with($this->parser->getResourceIncludePaths(Artist::$allowedIncludePaths, ArtistResource::$resourceType))
                        ->get(),
                    $this->parser
                )
            ),
            EntryCollection::$wrap => $this->when(
                $this->parser->hasSearch() && $this->isAllowedField(EntryCollection::$wrap),
                EntryCollection::make(
                    Entry::search($this->parser->getSearch())
                        ->with($this->parser->getResourceIncludePaths(Entry::$allowedIncludePaths, EntryResource::$resourceType))
                        ->get(),
                    $this->parser
                )
            ),
            SeriesCollection::$wrap => $this->when(
                $this->parser->hasSearch() && $this->isAllowedField(SeriesCollection::$wrap),
                SeriesCollection::make(
                    Series::search($this->parser->getSearch())
                        ->with($this->parser->getResourceIncludePaths(Series::$allowedIncludePaths, SeriesResource::$resourceType))
                        ->get(),
                    $this->parser
                )
            ),
            SongCollection::$wrap => $this->when(
                $this->parser->hasSearch() && $this->isAllowedField(SongCollection::$wrap),
                SongCollection::make(
                    Song::search($this->parser->getSearch())
                        ->with($this->parser->getResourceIncludePaths(Song::$allowedIncludePaths, SongResource::$resourceType))
                        ->get(),
                    $this->parser
                )
            ),
            SynonymCollection::$wrap => $this->when(
                $this->parser->hasSearch() && $this->isAllowedField(SynonymCollection::$wrap),
                SynonymCollection::make(
                    Synonym::search($this->parser->getSearch())
                        ->with($this->parser->getResourceIncludePaths(Synonym::$allowedIncludePaths, SynonymResource::$resourceType))
                        ->get(),
                    $this->parser
                )
            ),
            ThemeCollection::$wrap => $this->when(
                $this->parser->hasSearch() && $this->isAllowedField(ThemeCollection::$wrap),
                ThemeCollection::make(
                    Theme::search($this->parser->getSearch())
                        ->with($this->parser->getResourceIncludePaths(Theme::$allowedIncludePaths, ThemeResource::$resourceType))
                        ->get(),
                    $this->parser
                )
            ),
            VideoCollection::$wrap => $this->when(
                $this->parser->hasSearch() && $this->isAllowedField(VideoCollection::$wrap),
                VideoCollection::make(
                    Video::search($this->parser->getSearch())
                        ->with($this->parser->getResourceIncludePaths(Video::$allowedIncludePaths, VideoResource::$resourceType))
                        ->get(),
                    $this->parser
                )
            ),
        ];
    }
}
