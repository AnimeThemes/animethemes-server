<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Api\Criteria\Filter\Criteria as FilterCriteria;
use App\Http\Api\Criteria\Sort\Criteria;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Filter\Wiki\Studio\StudioIdFilter;
use App\Http\Api\Filter\Wiki\Studio\StudioNameFilter;
use App\Http\Api\Filter\Wiki\Studio\StudioSlugFilter;
use App\Http\Api\Sort\Sort;
use App\Http\Api\Sort\Wiki\Studio\StudioIdSort;
use App\Http\Api\Sort\Wiki\Studio\StudioNameSort;
use App\Http\Api\Sort\Wiki\Studio\StudioSlugSort;
use App\Http\Resources\SearchableCollection;
use App\Http\Resources\Wiki\Resource\StudioResource;
use App\Models\Wiki\Studio;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class StudioCollection.
 */
class StudioCollection extends SearchableCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'studios';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = Studio::class;

    /**
     * Transform the resource into a JSON array.
     *
     * @param Request $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return $this->collection->map(function (Studio $studio) {
            return StudioResource::make($studio, $this->query);
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
     * @param Collection<Criteria> $sortCriteria
     * @return Sort[]
     */
    public static function sorts(Collection $sortCriteria): array
    {
        return array_merge(
            parent::sorts($sortCriteria),
            [
                new StudioIdSort($sortCriteria),
                new StudioNameSort($sortCriteria),
                new StudioSlugSort($sortCriteria),
            ]
        );
    }

    /**
     * The filters that can be applied by the client for this resource.
     *
     * @param Collection<FilterCriteria> $filterCriteria
     * @return Filter[]
     */
    public static function filters(Collection $filterCriteria): array
    {
        return array_merge(
            parent::filters($filterCriteria),
            [
                new StudioIdFilter($filterCriteria),
                new StudioNameFilter($filterCriteria),
                new StudioSlugFilter($filterCriteria),
            ]
        );
    }
}
