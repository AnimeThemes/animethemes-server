<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Query;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Anime\Theme\Collection\EntryCollection;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Http\Resources\Wiki\Collection\SeriesCollection;
use App\Http\Resources\Wiki\Collection\SongCollection;
use App\Http\Resources\Wiki\Anime\Collection\SynonymCollection;
use App\Http\Resources\Wiki\Anime\Collection\ThemeCollection;
use App\Http\Resources\Wiki\Collection\VideoCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class SearchResource.
 */
class SearchResource extends BaseResource
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
     * @param Query $query
     * @return void
     */
    public function __construct(Query $query)
    {
        parent::__construct(new MissingValue(), $query);
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return [
            AnimeCollection::$wrap => $this->when(
                $this->isAllowedField(AnimeCollection::$wrap),
                AnimeCollection::performSearch($this->query, PaginationStrategy::LIMIT())
            ),
            ArtistCollection::$wrap => $this->when(
                $this->isAllowedField(ArtistCollection::$wrap),
                ArtistCollection::performSearch($this->query, PaginationStrategy::LIMIT())
            ),
            EntryCollection::$wrap => $this->when(
                $this->isAllowedField(EntryCollection::$wrap),
                EntryCollection::performSearch($this->query, PaginationStrategy::LIMIT())
            ),
            SeriesCollection::$wrap => $this->when(
                $this->isAllowedField(SeriesCollection::$wrap),
                SeriesCollection::performSearch($this->query, PaginationStrategy::LIMIT())
            ),
            SongCollection::$wrap => $this->when(
                $this->isAllowedField(SongCollection::$wrap),
                SongCollection::performSearch($this->query, PaginationStrategy::LIMIT())
            ),
            SynonymCollection::$wrap => $this->when(
                $this->isAllowedField(SynonymCollection::$wrap),
                SynonymCollection::performSearch($this->query, PaginationStrategy::LIMIT())
            ),
            ThemeCollection::$wrap => $this->when(
                $this->isAllowedField(ThemeCollection::$wrap),
                ThemeCollection::performSearch($this->query, PaginationStrategy::LIMIT())
            ),
            VideoCollection::$wrap => $this->when(
                $this->isAllowedField(VideoCollection::$wrap),
                VideoCollection::performSearch($this->query, PaginationStrategy::LIMIT())
            ),
        ];
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @return string[]
     */
    public static function allowedIncludePaths(): array
    {
        return [];
    }
}
