<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Enums\Http\Api\Field\Category;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\IntField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\StudioResource;
use App\Models\Wiki\Studio;
use App\Pivots\StudioResource as StudioResourcePivot;

/**
 * Class StudioSchema.
 */
class StudioSchema extends Schema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return StudioResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [
            AllowedInclude::make(AnimeSchema::class, Studio::RELATION_ANIME),
            AllowedInclude::make(ExternalResourceSchema::class, Studio::RELATION_RESOURCES),
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
                new IntField(BaseResource::ATTRIBUTE_ID, Studio::ATTRIBUTE_ID),
                new StringField(Studio::ATTRIBUTE_NAME),
                new StringField(Studio::ATTRIBUTE_SLUG),
                new StringField(StudioResourcePivot::ATTRIBUTE_AS, null, Category::COMPUTED()),
            ],
        );
    }
}
