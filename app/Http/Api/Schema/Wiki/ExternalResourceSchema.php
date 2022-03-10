<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\ExternalResource\ExternalResourceAsField;
use App\Http\Api\Field\Wiki\ExternalResource\ExternalResourceIdColumn;
use App\Http\Api\Field\Wiki\ExternalResource\ExternalResourceLinkColumn;
use App\Http\Api\Field\Wiki\ExternalResource\ExternalResourceSiteColumn;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Resources\Wiki\Resource\ExternalResourceResource;
use App\Models\Wiki\ExternalResource;

/**
 * Class ExternalResourceSchema.
 */
class ExternalResourceSchema extends EloquentSchema
{
    /**
     * The model this schema represents.
     *
     * @return string
     */
    public function model(): string
    {
        return ExternalResource::class;
    }

    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return ExternalResourceResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [
            AllowedInclude::make(AnimeSchema::class, ExternalResource::RELATION_ANIME),
            AllowedInclude::make(ArtistSchema::class, ExternalResource::RELATION_ARTISTS),
            AllowedInclude::make(StudioSchema::class, ExternalResource::RELATION_STUDIOS),
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
                new IdField(ExternalResource::ATTRIBUTE_ID),
                new ExternalResourceIdColumn(),
                new ExternalResourceLinkColumn(),
                new ExternalResourceSiteColumn(),
                new ExternalResourceAsField(),
            ],
        );
    }
}
