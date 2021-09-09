<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Api\Criteria\Filter\Criteria as FilterCriteria;
use App\Http\Api\Criteria\Sort\Criteria;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Filter\Wiki\Image\ImageFacetFilter;
use App\Http\Api\Filter\Wiki\Image\ImageIdFilter;
use App\Http\Api\Filter\Wiki\Image\ImageMimeTypeFilter;
use App\Http\Api\Filter\Wiki\Image\ImagePathFilter;
use App\Http\Api\Filter\Wiki\Image\ImageSizeFilter;
use App\Http\Api\Sort\Sort;
use App\Http\Api\Sort\Wiki\Image\ImageFacetSort;
use App\Http\Api\Sort\Wiki\Image\ImageIdSort;
use App\Http\Api\Sort\Wiki\Image\ImageMimeTypeSort;
use App\Http\Api\Sort\Wiki\Image\ImagePathSort;
use App\Http\Api\Sort\Wiki\Image\ImageSizeSort;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Resource\ImageResource;
use App\Models\Wiki\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class ImageCollection.
 */
class ImageCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'images';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = Image::class;

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
        return $this->collection->map(function (Image $image) {
            return ImageResource::make($image, $this->query);
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
                new ImageIdSort($sortCriteria),
                new ImagePathSort($sortCriteria),
                new ImageSizeSort($sortCriteria),
                new ImageMimeTypeSort($sortCriteria),
                new ImageFacetSort($sortCriteria),
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
                new ImageIdFilter($filterCriteria),
                new ImagePathFilter($filterCriteria),
                new ImageSizeFilter($filterCriteria),
                new ImageMimeTypeFilter($filterCriteria),
                new ImageFacetFilter($filterCriteria),
            ]
        );
    }
}
