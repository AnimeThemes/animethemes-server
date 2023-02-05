<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Image\ImageFacetField;
use App\Http\Api\Field\Wiki\Image\ImageFileField;
use App\Http\Api\Field\Wiki\Image\ImageIdField;
use App\Http\Api\Field\Wiki\Image\ImageLinkField;
use App\Http\Api\Field\Wiki\Image\ImagePathField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Resources\Wiki\Resource\ImageResource;
use App\Models\Wiki\Image;

/**
 * Class ImageSchema.
 */
class ImageSchema extends EloquentSchema
{
    /**
     * The model this schema represents.
     *
     * @return string
     */
    public function model(): string
    {
        return Image::class;
    }

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
            new AllowedInclude(new AnimeSchema(), Image::RELATION_ANIME),
            new AllowedInclude(new ArtistSchema(), Image::RELATION_ARTISTS),
            new AllowedInclude(new StudioSchema(), Image::RELATION_STUDIOS),
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
                new ImageIdField($this),
                new ImageFacetField($this),
                new ImagePathField($this),
                new ImageLinkField($this),
                new ImageFileField($this),
            ],
        );
    }
}
