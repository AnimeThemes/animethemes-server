<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\ImageSchema;
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
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return $this->collection->map(fn (Image $image) => ImageResource::make($image, $this->query))->all();
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public static function schema(): Schema
    {
        return new ImageSchema();
    }
}
