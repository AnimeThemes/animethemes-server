<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Api\Criteria\Filter\Criteria as FilterCriteria;
use App\Http\Api\Criteria\Sort\Criteria;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Filter\Wiki\ExternalResource\ExternalResourceExternalIdFilter;
use App\Http\Api\Filter\Wiki\ExternalResource\ExternalResourceIdFilter;
use App\Http\Api\Filter\Wiki\ExternalResource\ExternalResourceLinkFilter;
use App\Http\Api\Filter\Wiki\ExternalResource\ExternalResourceSiteFilter;
use App\Http\Api\Sort\Sort;
use App\Http\Api\Sort\Wiki\ExternalResource\ExternalResourceExternalIdSort;
use App\Http\Api\Sort\Wiki\ExternalResource\ExternalResourceIdSort;
use App\Http\Api\Sort\Wiki\ExternalResource\ExternalResourceLinkSort;
use App\Http\Api\Sort\Wiki\ExternalResource\ExternalResourceSiteSort;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Resource\ExternalResourceResource;
use App\Models\Wiki\ExternalResource;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class ExternalResourceCollection.
 */
class ExternalResourceCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'resources';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = ExternalResource::class;

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
        return $this->collection->map(function (ExternalResource $resource) {
            return ExternalResourceResource::make($resource, $this->query);
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
                new ExternalResourceIdSort($sortCriteria),
                new ExternalResourceLinkSort($sortCriteria),
                new ExternalResourceExternalIdSort($sortCriteria),
                new ExternalResourceSiteSort($sortCriteria),
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
                new ExternalResourceIdFilter($filterCriteria),
                new ExternalResourceLinkFilter($filterCriteria),
                new ExternalResourceExternalIdFilter($filterCriteria),
                new ExternalResourceSiteFilter($filterCriteria),
            ]
        );
    }
}
