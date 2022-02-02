<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Query\Wiki\Anime\ThemeQuery;
use App\Http\Api\Query\Wiki\AnimeQuery;
use App\Http\Api\Query\Wiki\ArtistQuery;
use App\Http\Api\Query\Wiki\SearchQuery;
use App\Http\Api\Query\Wiki\SeriesQuery;
use App\Http\Api\Query\Wiki\SongQuery;
use App\Http\Api\Query\Wiki\StudioQuery;
use App\Http\Api\Query\Wiki\VideoQuery;
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
use Illuminate\Support\Arr;

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
     * @param  SearchQuery  $query
     * @return void
     */
    public function __construct(protected SearchQuery $query)
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
            $animeQuery = $this->query->getQuery(AnimeQuery::class);
            if ($animeQuery !== null) {
                Arr::set($result,
                    AnimeCollection::$wrap,
                    $animeQuery->search(PaginationStrategy::LIMIT())
                );
            }
        }

        if ($this->isAllowedField(ThemeCollection::$wrap)) {
            $themeQuery = $this->query->getQuery(ThemeQuery::class);
            if ($themeQuery !== null) {
                Arr::set($result,
                    ThemeCollection::$wrap,
                    $themeQuery->search(PaginationStrategy::LIMIT())
                );
            }
        }

        if ($this->isAllowedField(ArtistCollection::$wrap)) {
            $artistQuery = $this->query->getQuery(ArtistQuery::class);
            if ($artistQuery !== null) {
                Arr::set($result,
                    ArtistCollection::$wrap,
                    $artistQuery->search(PaginationStrategy::LIMIT())
                );
            }
        }

        if ($this->isAllowedField(SeriesCollection::$wrap)) {
            $seriesQuery = $this->query->getQuery(SeriesQuery::class);
            if ($seriesQuery !== null) {
                Arr::set($result,
                    SeriesCollection::$wrap,
                    $seriesQuery->search(PaginationStrategy::LIMIT())
                );
            }
        }

        if ($this->isAllowedField(SongCollection::$wrap)) {
            $songQuery = $this->query->getQuery(SongQuery::class);
            if ($songQuery !== null) {
                Arr::set($result,
                    SongCollection::$wrap,
                    $songQuery->search(PaginationStrategy::LIMIT())
                );
            }
        }

        if ($this->isAllowedField(StudioCollection::$wrap)) {
            $studioQuery = $this->query->getQuery(StudioQuery::class);
            if ($studioQuery !== null) {
                Arr::set($result,
                    StudioCollection::$wrap,
                    $studioQuery->search(PaginationStrategy::LIMIT())
                );
            }
        }

        if ($this->isAllowedField(VideoCollection::$wrap)) {
            $videoQuery = $this->query->getQuery(VideoQuery::class);
            if ($videoQuery !== null) {
                Arr::set($result,
                    VideoCollection::$wrap,
                    $videoQuery->search(PaginationStrategy::LIMIT())
                );
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
