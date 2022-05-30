<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Query\Wiki\Anime\AnimeReadQuery;
use App\Http\Api\Query\Wiki\Anime\Theme\ThemeReadQuery;
use App\Http\Api\Query\Wiki\Artist\ArtistReadQuery;
use App\Http\Api\Query\Wiki\SearchReadQuery;
use App\Http\Api\Query\Wiki\Series\SeriesReadQuery;
use App\Http\Api\Query\Wiki\Song\SongReadQuery;
use App\Http\Api\Query\Wiki\Studio\StudioReadQuery;
use App\Http\Api\Query\Wiki\Video\VideoReadQuery;
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
     * @param  SearchReadQuery  $query
     * @return void
     */
    public function __construct(protected SearchReadQuery $query)
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
    public function toArray($request): array
    {
        // Every search may raise a query on another machine, so we will proactively check sparse fieldsets.
        $result = [];

        if ($this->isAllowedField(AnimeCollection::$wrap)) {
            $animeQuery = $this->query->getQuery(AnimeReadQuery::class);
            if ($animeQuery !== null) {
                $result[AnimeCollection::$wrap] = $animeQuery->search(PaginationStrategy::LIMIT());
            }
        }

        if ($this->isAllowedField(ThemeCollection::$wrap)) {
            $themeQuery = $this->query->getQuery(ThemeReadQuery::class);
            if ($themeQuery !== null) {
                $result[ThemeCollection::$wrap] = $themeQuery->search(PaginationStrategy::LIMIT());
            }
        }

        if ($this->isAllowedField(ArtistCollection::$wrap)) {
            $artistQuery = $this->query->getQuery(ArtistReadQuery::class);
            if ($artistQuery !== null) {
                $result[ArtistCollection::$wrap] = $artistQuery->search(PaginationStrategy::LIMIT());
            }
        }

        if ($this->isAllowedField(SeriesCollection::$wrap)) {
            $seriesQuery = $this->query->getQuery(SeriesReadQuery::class);
            if ($seriesQuery !== null) {
                $result[SeriesCollection::$wrap] = $seriesQuery->search(PaginationStrategy::LIMIT());
            }
        }

        if ($this->isAllowedField(SongCollection::$wrap)) {
            $songQuery = $this->query->getQuery(SongReadQuery::class);
            if ($songQuery !== null) {
                $result[SongCollection::$wrap] = $songQuery->search(PaginationStrategy::LIMIT());
            }
        }

        if ($this->isAllowedField(StudioCollection::$wrap)) {
            $studioQuery = $this->query->getQuery(StudioReadQuery::class);
            if ($studioQuery !== null) {
                $result[StudioCollection::$wrap] = $studioQuery->search(PaginationStrategy::LIMIT());
            }
        }

        if ($this->isAllowedField(VideoCollection::$wrap)) {
            $videoQuery = $this->query->getQuery(VideoReadQuery::class);
            if ($videoQuery !== null) {
                $result[VideoCollection::$wrap] = $videoQuery->search(PaginationStrategy::LIMIT());
            }
        }

        return $result;
    }

    /**
     * Determine if field should be included in the response for this resource.
     *
     * @param  string  $field
     * @return bool
     */
    protected function isAllowedField(string $field): bool
    {
        $criteria = $this->query->getFieldCriteria(static::$wrap);

        return $criteria === null || $criteria->isAllowedField($field);
    }
}
