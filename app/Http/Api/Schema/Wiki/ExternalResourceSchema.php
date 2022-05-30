<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\ExternalResource\ExternalResourceAsField;
use App\Http\Api\Field\Wiki\ExternalResource\ExternalResourceIdField;
use App\Http\Api\Field\Wiki\ExternalResource\ExternalResourceLinkField;
use App\Http\Api\Field\Wiki\ExternalResource\ExternalResourceSiteField;
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
            new AllowedInclude(new AnimeSchema(), ExternalResource::RELATION_ANIME),
            new AllowedInclude(new ArtistSchema(), ExternalResource::RELATION_ARTISTS),
            new AllowedInclude(new StudioSchema(), ExternalResource::RELATION_STUDIOS),
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
                new ExternalResourceIdField(),
                new ExternalResourceLinkField(),
                new ExternalResourceSiteField(),
                new ExternalResourceAsField(),
            ],
        );
    }
}
