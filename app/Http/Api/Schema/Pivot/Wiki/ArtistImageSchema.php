<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Pivot\Wiki;

use App\Http\Api\Field\Base\CreatedAtField;
use App\Http\Api\Field\Base\UpdatedAtField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Pivot\Wiki\ArtistImage\ArtistImageArtistIdField;
use App\Http\Api\Field\Pivot\Wiki\ArtistImage\ArtistImageImageIdField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistImageResource;
use App\Pivots\Wiki\ArtistImage;

/**
 * Class ArtistImageSchema.
 */
class ArtistImageSchema extends EloquentSchema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return ArtistImageResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return array_merge(
            $this->withIntermediatePaths([
                new AllowedInclude(new ArtistSchema(), ArtistImage::RELATION_ARTIST),
                new AllowedInclude(new ImageSchema(), ArtistImage::RELATION_IMAGE),
            ]),
            []
        );
    }

    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fields(): array
    {
        return [
            new CreatedAtField($this),
            new UpdatedAtField($this),
            new ArtistImageArtistIdField($this),
            new ArtistImageImageIdField($this),
        ];
    }
}
