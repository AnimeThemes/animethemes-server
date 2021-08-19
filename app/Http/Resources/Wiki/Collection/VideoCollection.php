<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Api\Criteria\Filter\Criteria as FilterCriteria;
use App\Http\Api\Criteria\Sort\Criteria;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Filter\Wiki\Video\VideoBasenameFilter;
use App\Http\Api\Filter\Wiki\Video\VideoFilenameFilter;
use App\Http\Api\Filter\Wiki\Video\VideoIdFilter;
use App\Http\Api\Filter\Wiki\Video\VideoLyricsFilter;
use App\Http\Api\Filter\Wiki\Video\VideoMimeTypeFilter;
use App\Http\Api\Filter\Wiki\Video\VideoNcFilter;
use App\Http\Api\Filter\Wiki\Video\VideoOverlapFilter;
use App\Http\Api\Filter\Wiki\Video\VideoPathFilter;
use App\Http\Api\Filter\Wiki\Video\VideoResolutionFilter;
use App\Http\Api\Filter\Wiki\Video\VideoSizeFilter;
use App\Http\Api\Filter\Wiki\Video\VideoSourceFilter;
use App\Http\Api\Filter\Wiki\Video\VideoSubbedFilter;
use App\Http\Api\Filter\Wiki\Video\VideoUncenFilter;
use App\Http\Api\Sort\Sort;
use App\Http\Api\Sort\Wiki\Video\VideoBasenameSort;
use App\Http\Api\Sort\Wiki\Video\VideoFilenameSort;
use App\Http\Api\Sort\Wiki\Video\VideoIdSort;
use App\Http\Api\Sort\Wiki\Video\VideoLyricsSort;
use App\Http\Api\Sort\Wiki\Video\VideoMimeTypeSort;
use App\Http\Api\Sort\Wiki\Video\VideoNcSort;
use App\Http\Api\Sort\Wiki\Video\VideoOverlapSort;
use App\Http\Api\Sort\Wiki\Video\VideoPathSort;
use App\Http\Api\Sort\Wiki\Video\VideoResolutionSort;
use App\Http\Api\Sort\Wiki\Video\VideoSizeSort;
use App\Http\Api\Sort\Wiki\Video\VideoSourceSort;
use App\Http\Api\Sort\Wiki\Video\VideoSubbedSort;
use App\Http\Api\Sort\Wiki\Video\VideoUncenSort;
use App\Http\Resources\SearchableCollection;
use App\Http\Resources\Wiki\Resource\VideoResource;
use App\Models\Wiki\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class VideoCollection.
 */
class VideoCollection extends SearchableCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'videos';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = Video::class;

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
        return $this->collection->map(function (Video $video) {
            return VideoResource::make($video, $this->query);
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
            'animethemeentries',
            'animethemeentries.animetheme',
            'animethemeentries.animetheme.anime',
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
                new VideoIdSort($sortCriteria),
                new VideoBasenameSort($sortCriteria),
                new VideoFilenameSort($sortCriteria),
                new VideoPathSort($sortCriteria),
                new VideoSizeSort($sortCriteria),
                new VideoMimeTypeSort($sortCriteria),
                new VideoResolutionSort($sortCriteria),
                new VideoNcSort($sortCriteria),
                new VideoSubbedSort($sortCriteria),
                new VideoLyricsSort($sortCriteria),
                new VideoUncenSort($sortCriteria),
                new VideoSourceSort($sortCriteria),
                new VideoOverlapSort($sortCriteria),
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
                new VideoIdFilter($filterCriteria),
                new VideoBasenameFilter($filterCriteria),
                new VideoFilenameFilter($filterCriteria),
                new VideoPathFilter($filterCriteria),
                new VideoSizeFilter($filterCriteria),
                new VideoMimeTypeFilter($filterCriteria),
                new VideoResolutionFilter($filterCriteria),
                new VideoNcFilter($filterCriteria),
                new VideoSubbedFilter($filterCriteria),
                new VideoLyricsFilter($filterCriteria),
                new VideoUncenFilter($filterCriteria),
                new VideoSourceFilter($filterCriteria),
                new VideoOverlapFilter($filterCriteria),
            ]
        );
    }
}
