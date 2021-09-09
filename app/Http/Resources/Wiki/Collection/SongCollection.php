<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Api\Criteria\Filter\Criteria as FilterCriteria;
use App\Http\Api\Criteria\Sort\Criteria;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Filter\Wiki\Song\SongIdFilter;
use App\Http\Api\Filter\Wiki\Song\SongTitleFilter;
use App\Http\Api\Sort\Sort;
use App\Http\Api\Sort\Wiki\Song\SongIdSort;
use App\Http\Api\Sort\Wiki\Song\SongTitleSort;
use App\Http\Resources\SearchableCollection;
use App\Http\Resources\Wiki\Resource\SongResource;
use App\Models\Wiki\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class SongCollection.
 */
class SongCollection extends SearchableCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'songs';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = Song::class;

    /**
     * Transform the resource into a JSON array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return $this->collection->map(function (Song $song) {
            return SongResource::make($song, $this->query);
        })->all();
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @return string[]
     */
    public static function allowedIncludePaths(): array
    {
        return [
            'animethemes',
            'animethemes.anime',
            'artists',
        ];
    }

    /**
     * The sorts that can be applied by the client for this resource.
     *
     * @param  Collection<Criteria>  $sortCriteria
     * @return Sort[]
     */
    public static function sorts(Collection $sortCriteria): array
    {
        return array_merge(
            parent::sorts($sortCriteria),
            [
                new SongIdSort($sortCriteria),
                new SongTitleSort($sortCriteria),
            ]
        );
    }

    /**
     * The filters that can be applied by the client for this resource.
     *
     * @param  Collection<FilterCriteria>  $filterCriteria
     * @return Filter[]
     */
    public static function filters(Collection $filterCriteria): array
    {
        return array_merge(
            parent::filters($filterCriteria),
            [
                new SongIdFilter($filterCriteria),
                new SongTitleFilter($filterCriteria),
            ]
        );
    }
}
