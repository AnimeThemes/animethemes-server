<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Models\BaseModel;
use App\Models\Wiki\Image;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class ImageResource.
 *
 * @mixin Image
 */
class ImageResource extends BaseResource
{
    public const ATTRIBUTE_LINK = 'link';

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'image';

    /**
     * Create a new resource instance.
     *
     * @param  Image | MissingValue | null  $image
     * @param  Query  $query
     * @return void
     */
    public function __construct(Image|MissingValue|null $image, Query $query)
    {
        parent::__construct($image, $query);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return [
            BaseResource::ATTRIBUTE_ID => $this->when($this->isAllowedField(BaseResource::ATTRIBUTE_ID), $this->getKey()),
            Image::ATTRIBUTE_PATH => $this->when($this->isAllowedField(Image::ATTRIBUTE_PATH), $this->path),
            Image::ATTRIBUTE_SIZE => $this->when($this->isAllowedField(Image::ATTRIBUTE_SIZE), $this->size),
            Image::ATTRIBUTE_MIMETYPE => $this->when($this->isAllowedField(Image::ATTRIBUTE_MIMETYPE), $this->mimetype),
            Image::ATTRIBUTE_FACET => $this->when($this->isAllowedField(Image::ATTRIBUTE_FACET), $this->facet?->description),
            BaseModel::ATTRIBUTE_CREATED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_CREATED_AT), $this->created_at),
            BaseModel::ATTRIBUTE_UPDATED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_UPDATED_AT), $this->updated_at),
            BaseModel::ATTRIBUTE_DELETED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_DELETED_AT), $this->deleted_at),
            ImageResource::ATTRIBUTE_LINK =>  $this->when($this->isAllowedField(ImageResource::ATTRIBUTE_LINK), route('image.show', $this)),
            Image::RELATION_ARTISTS => ArtistCollection::make($this->whenLoaded(Image::RELATION_ARTISTS), $this->query),
            Image::RELATION_ANIME => AnimeCollection::make($this->whenLoaded(Image::RELATION_ANIME), $this->query),
        ];
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
