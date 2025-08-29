<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Contracts\Http\Api\Schema\InteractsWithPivots;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Image\ImageFacetField;
use App\Http\Api\Field\Wiki\Image\ImageFileField;
use App\Http\Api\Field\Wiki\Image\ImageIdField;
use App\Http\Api\Field\Wiki\Image\ImageLinkField;
use App\Http\Api\Field\Wiki\Image\ImagePathField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Pivot\Morph\ImageableSchema;
use App\Http\Resources\Wiki\Resource\ImageResource;
use App\Models\Wiki\Image;

class ImageSchema extends EloquentSchema implements InteractsWithPivots
{
    /**
     * @return AllowedInclude[]
     */
    public function allowedPivots(): array
    {
        return [
            new AllowedInclude(new ImageableSchema(new ArtistSchema(), 'artistimage'), 'artistimage'),
        ];
    }

    public function type(): string
    {
        return ImageResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new AnimeSchema(), Image::RELATION_ANIME),
            new AllowedInclude(new ArtistSchema(), Image::RELATION_ARTISTS),
            new AllowedInclude(new StudioSchema(), Image::RELATION_STUDIOS),
        ]);
    }

    /**
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
