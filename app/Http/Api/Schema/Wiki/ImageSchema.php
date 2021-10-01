<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Enums\Http\Api\Field\Category;
use App\Enums\Models\Wiki\ImageFacet;
use App\Http\Api\Field\EnumField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\IntField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\ImageResource;
use App\Models\Wiki\Image;

/**
 * Class ImageSchema.
 */
class ImageSchema extends Schema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return ImageResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [
            AllowedInclude::make(AnimeSchema::class, Image::RELATION_ANIME),
            AllowedInclude::make(ArtistSchema::class, Image::RELATION_ARTISTS),
        ];
    }

    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return array_merge(
            parent::fields(),
            [
                new IntField(BaseResource::ATTRIBUTE_ID, Image::ATTRIBUTE_ID),
                new EnumField(Image::ATTRIBUTE_FACET, ImageFacet::class),
                new StringField(Image::ATTRIBUTE_MIMETYPE),
                new StringField(Image::ATTRIBUTE_PATH),
                new IntField(Image::ATTRIBUTE_SIZE),
                new StringField(ImageResource::ATTRIBUTE_LINK, null, Category::COMPUTED()),
            ],
        );
    }
}
