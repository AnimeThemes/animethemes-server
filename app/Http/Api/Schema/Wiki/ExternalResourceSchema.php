<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Enums\Http\Api\Field\Category;
use App\Enums\Models\Wiki\ResourceSite;
use App\Http\Api\Field\EnumField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\IntField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\ExternalResourceResource;
use App\Models\Wiki\ExternalResource;
use App\Pivots\AnimeResource;

/**
 * Class ExternalResourceSchema.
 */
class ExternalResourceSchema extends Schema
{
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
            AllowedInclude::make(StudioSchema::class, ExternalResource::RELATION_STUDIOS)
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
                new IntField(BaseResource::ATTRIBUTE_ID, ExternalResource::ATTRIBUTE_ID),
                new IntField(ExternalResource::ATTRIBUTE_EXTERNAL_ID),
                new StringField(ExternalResource::ATTRIBUTE_LINK),
                new EnumField(ExternalResource::ATTRIBUTE_SITE, ResourceSite::class),
                new StringField(AnimeResource::ATTRIBUTE_AS, null, Category::COMPUTED()),
            ],
        );
    }
}
