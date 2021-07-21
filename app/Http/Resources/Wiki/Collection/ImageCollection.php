<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Api\Filter\Wiki\Image\ImageFacetFilter;
use App\Http\Api\Filter\Wiki\Image\ImageMimeTypeFilter;
use App\Http\Api\Filter\Wiki\Image\ImagePathFilter;
use App\Http\Api\Filter\Wiki\Image\ImageSizeFilter;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Resource\ImageResource;
use App\Models\Wiki\Image;
use Illuminate\Http\Request;

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
     * @param Request $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return $this->collection->map(function (Image $image) {
            return ImageResource::make($image, $this->parser);
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
     * The sort field names a client is allowed to request.
     *
     * @return string[]
     */
    public static function allowedSortFields(): array
    {
        return [
            'image_id',
            'created_at',
            'updated_at',
            'deleted_at',
            'path',
            'size',
            'mimetype',
            'facet',
        ];
    }

    /**
     * The filters that can be applied by the client for this resource.
     *
     * @return string[]
     */
    public static function filters(): array
    {
        return array_merge(
            parent::filters(),
            [
                ImagePathFilter::class,
                ImageSizeFilter::class,
                ImageMimeTypeFilter::class,
                ImageFacetFilter::class,
            ]
        );
    }
}
