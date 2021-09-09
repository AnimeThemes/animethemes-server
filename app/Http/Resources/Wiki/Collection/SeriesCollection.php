<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Api\Criteria\Filter\Criteria as FilterCriteria;
use App\Http\Api\Criteria\Sort\Criteria;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Filter\Wiki\Series\SeriesIdFilter;
use App\Http\Api\Filter\Wiki\Series\SeriesNameFilter;
use App\Http\Api\Filter\Wiki\Series\SeriesSlugFilter;
use App\Http\Api\Sort\Sort;
use App\Http\Api\Sort\Wiki\Series\SeriesIdSort;
use App\Http\Api\Sort\Wiki\Series\SeriesNameSort;
use App\Http\Api\Sort\Wiki\Series\SeriesSlugSort;
use App\Http\Resources\SearchableCollection;
use App\Http\Resources\Wiki\Resource\SeriesResource;
use App\Models\Wiki\Series;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class SeriesCollection.
 */
class SeriesCollection extends SearchableCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'series';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = Series::class;

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
        return $this->collection->map(function (Series $series) {
            return SeriesResource::make($series, $this->query);
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
            'anime',
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
                new SeriesIdSort($sortCriteria),
                new SeriesNameSort($sortCriteria),
                new SeriesSlugSort($sortCriteria),
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
                new SeriesIdFilter($filterCriteria),
                new SeriesNameFilter($filterCriteria),
                new SeriesSlugFilter($filterCriteria),
            ]
        );
    }
}
