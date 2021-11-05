<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Anime\Collection\ThemeCollection;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Http\Resources\Wiki\Collection\SeriesCollection;
use App\Http\Resources\Wiki\Collection\SongCollection;
use App\Http\Resources\Wiki\Collection\StudioCollection;
use App\Http\Resources\Wiki\Collection\VideoCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Arr;

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
     * @param  Query  $query
     * @return void
     */
    public function __construct(Query $query)
    {
        parent::__construct(new MissingValue(), $query);
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
        $result = [];

        if ($this->isAllowedField(AnimeCollection::$wrap)) {
            Arr::set($result,
                AnimeCollection::$wrap,
                AnimeCollection::performSearch($this->query, PaginationStrategy::LIMIT())
            );
        }

        if ($this->isAllowedField(ThemeCollection::$wrap)) {
            Arr::set($result,
                ThemeCollection::$wrap,
                ThemeCollection::performSearch($this->query, PaginationStrategy::LIMIT())
            );
        }

        if ($this->isAllowedField(ArtistCollection::$wrap)) {
            Arr::set($result,
                ArtistCollection::$wrap,
                ArtistCollection::performSearch($this->query, PaginationStrategy::LIMIT())
            );
        }

        if ($this->isAllowedField(SeriesCollection::$wrap)) {
            Arr::set($result,
                SeriesCollection::$wrap,
                SeriesCollection::performSearch($this->query, PaginationStrategy::LIMIT())
            );
        }

        if ($this->isAllowedField(SongCollection::$wrap)) {
            Arr::set($result,
                SongCollection::$wrap,
                SongCollection::performSearch($this->query, PaginationStrategy::LIMIT())
            );
        }

        if ($this->isAllowedField(StudioCollection::$wrap)) {
            Arr::set($result,
                StudioCollection::$wrap,
                StudioCollection::performSearch($this->query, PaginationStrategy::LIMIT())
            );
        }

        if ($this->isAllowedField(VideoCollection::$wrap)) {
            Arr::set($result,
                VideoCollection::$wrap,
                VideoCollection::performSearch($this->query, PaginationStrategy::LIMIT())
            );
        }

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public static function schema(): Schema
    {
        return new AnimeSchema(); // TODO: SearchSchema or don't inherit from BaseResource
    }
}
