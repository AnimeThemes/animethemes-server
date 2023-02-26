<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Pivot\Wiki;

use App\Http\Api\Field\Base\CreatedAtField;
use App\Http\Api\Field\Base\UpdatedAtField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Pivot\Wiki\ArtistResource\ArtistResourceArtistIdField;
use App\Http\Api\Field\Pivot\Wiki\ArtistResource\ArtistResourceAsField;
use App\Http\Api\Field\Pivot\Wiki\ArtistResource\ArtistResourceResourceIdField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Api\Schema\Wiki\ExternalResourceSchema;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistResourceResource;
use App\Pivots\Wiki\ArtistResource;

/**
 * Class ArtistResourceSchema.
 */
class ArtistResourceSchema extends EloquentSchema
{
    /**
     * The model this schema represents.
     *
     * @return string
     */
    public function model(): string
    {
        return ArtistResource::class;
    }

    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return ArtistResourceResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [
            new AllowedInclude(new ArtistSchema(), ArtistResource::RELATION_ARTIST),
            new AllowedInclude(new ExternalResourceSchema(), ArtistResource::RELATION_RESOURCE),
        ];
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
            new ArtistResourceArtistIdField($this),
            new ArtistResourceResourceIdField($this),
            new ArtistResourceAsField($this),
        ];
    }
}
