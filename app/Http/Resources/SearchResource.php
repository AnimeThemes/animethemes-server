<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Actions\Http\Api\IndexAction;
use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\List\PlaylistSchema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Api\Schema\Wiki\SeriesSchema;
use App\Http\Api\Schema\Wiki\SongSchema;
use App\Http\Api\Schema\Wiki\StudioSchema;
use App\Http\Api\Schema\Wiki\VideoSchema;
use App\Http\Resources\List\Collection\PlaylistCollection;
use App\Http\Resources\Wiki\Anime\Collection\ThemeCollection;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Http\Resources\Wiki\Collection\SeriesCollection;
use App\Http\Resources\Wiki\Collection\SongCollection;
use App\Http\Resources\Wiki\Collection\StudioCollection;
use App\Http\Resources\Wiki\Collection\VideoCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class SearchResource.
 */
class SearchResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'search';

    /**
     * Create a new resource instance.
     *
     * @param  Query  $query
     * @return void
     */
    public function __construct(protected readonly Query $query)
    {
        parent::__construct(new MissingValue());
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        // Every search may raise a query on another machine, so we will proactively check sparse fieldsets.
        $result = [];

        $criteria = $this->query->getFieldCriteria(static::$wrap);
        if ($criteria === null) {
            return $result;
        }

        $action = new IndexAction();

        if ($criteria->isAllowedField(AnimeCollection::$wrap)) {
            $anime = $action->search($this->query, new AnimeSchema(), PaginationStrategy::LIMIT);
            $result[AnimeCollection::$wrap] = new AnimeCollection($anime, $this->query);
        }

        if ($criteria->isAllowedField(ThemeCollection::$wrap)) {
            $themes = $action->search($this->query, new ThemeSchema(), PaginationStrategy::LIMIT);
            $result[ThemeCollection::$wrap] = new ThemeCollection($themes, $this->query);
        }

        if ($criteria->isAllowedField(ArtistCollection::$wrap)) {
            $artists = $action->search($this->query, new ArtistSchema(), PaginationStrategy::LIMIT);
            $result[ArtistCollection::$wrap] = new ArtistCollection($artists, $this->query);
        }

        if ($criteria->isAllowedField(PlaylistCollection::$wrap)) {
            $series = $action->search($this->query, new PlaylistSchema(), PaginationStrategy::LIMIT);
            $result[PlaylistCollection::$wrap] = new PlaylistCollection($series, $this->query);
        }

        if ($criteria->isAllowedField(SeriesCollection::$wrap)) {
            $series = $action->search($this->query, new SeriesSchema(), PaginationStrategy::LIMIT);
            $result[SeriesCollection::$wrap] = new SeriesCollection($series, $this->query);
        }

        if ($criteria->isAllowedField(SongCollection::$wrap)) {
            $songs = $action->search($this->query, new SongSchema(), PaginationStrategy::LIMIT);
            $result[SongCollection::$wrap] = new SongCollection($songs, $this->query);
        }

        if ($criteria->isAllowedField(StudioCollection::$wrap)) {
            $studios = $action->search($this->query, new StudioSchema(), PaginationStrategy::LIMIT);
            $result[StudioCollection::$wrap] = new StudioCollection($studios, $this->query);
        }

        if ($criteria->isAllowedField(VideoCollection::$wrap)) {
            $videos = $action->search($this->query, new VideoSchema(), PaginationStrategy::LIMIT);
            $result[VideoCollection::$wrap] = new VideoCollection($videos, $this->query);
        }

        return $result;
    }
}
