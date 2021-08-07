<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\Collection;

use App\Http\Api\Criteria\Filter\Criteria as FilterCriteria;
use App\Http\Api\Criteria\Sort\Criteria;
use App\Http\Api\Filter\Admin\Announcement\AnnouncementContentFilter;
use App\Http\Api\Filter\Admin\Announcement\AnnouncementIdFilter;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Sort\Admin\Announcement\AnnouncementContentSort;
use App\Http\Api\Sort\Admin\Announcement\AnnouncementIdSort;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Admin\Resource\AnnouncementResource;
use App\Http\Resources\BaseCollection;
use App\Models\Admin\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class AnnouncementCollection.
 */
class AnnouncementCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'announcements';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = Announcement::class;

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
        return $this->collection->map(function (Announcement $announcement) {
            return AnnouncementResource::make($announcement, $this->query);
        })->all();
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
                new AnnouncementIdSort($sortCriteria),
                new AnnouncementContentSort($sortCriteria),
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
                new AnnouncementIdFilter($filterCriteria),
                new AnnouncementContentFilter($filterCriteria),
            ]
        );
    }
}
