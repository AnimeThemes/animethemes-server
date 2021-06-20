<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Enums\Http\Api\PaginationStrategy;
use App\Http\Api\QueryParser;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Http\Resources\Wiki\Collection\EntryCollection;
use App\Http\Resources\Wiki\Collection\SeriesCollection;
use App\Http\Resources\Wiki\Collection\SongCollection;
use App\Http\Resources\Wiki\Collection\SynonymCollection;
use App\Http\Resources\Wiki\Collection\ThemeCollection;
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
     * @var string
     */
    public static $wrap = 'search';

    /**
     * Create a new resource instance.
     *
     * @param QueryParser $parser
     * @return void
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct(new MissingValue(), $parser);
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            AnimeCollection::$wrap => $this->when(
                $this->isAllowedField(AnimeCollection::$wrap),
                AnimeCollection::performSearch($this->parser, PaginationStrategy::LIMIT())
            ),
            ArtistCollection::$wrap => $this->when(
                $this->isAllowedField(ArtistCollection::$wrap),
                ArtistCollection::performSearch($this->parser, PaginationStrategy::LIMIT())
            ),
            EntryCollection::$wrap => $this->when(
                $this->isAllowedField(EntryCollection::$wrap),
                EntryCollection::performSearch($this->parser, PaginationStrategy::LIMIT())
            ),
            SeriesCollection::$wrap => $this->when(
                $this->isAllowedField(SeriesCollection::$wrap),
                SeriesCollection::performSearch($this->parser, PaginationStrategy::LIMIT())
            ),
            SongCollection::$wrap => $this->when(
                $this->isAllowedField(SongCollection::$wrap),
                SongCollection::performSearch($this->parser, PaginationStrategy::LIMIT())
            ),
            SynonymCollection::$wrap => $this->when(
                $this->isAllowedField(SynonymCollection::$wrap),
                SynonymCollection::performSearch($this->parser, PaginationStrategy::LIMIT())
            ),
            ThemeCollection::$wrap => $this->when(
                $this->isAllowedField(ThemeCollection::$wrap),
                ThemeCollection::performSearch($this->parser, PaginationStrategy::LIMIT())
            ),
            VideoCollection::$wrap => $this->when(
                $this->isAllowedField(VideoCollection::$wrap),
                VideoCollection::performSearch($this->parser, PaginationStrategy::LIMIT())
            ),
        ];
    }
}
