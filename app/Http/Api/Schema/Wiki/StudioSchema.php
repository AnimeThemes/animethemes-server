<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Studio\StudioAsField;
use App\Http\Api\Field\Wiki\Studio\StudioNameField;
use App\Http\Api\Field\Wiki\Studio\StudioSlugField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Resources\Wiki\Resource\StudioResource;
use App\Models\Wiki\Studio;

/**
 * Class StudioSchema.
 */
class StudioSchema extends EloquentSchema
{
    /**
     * The model this schema represents.
     *
     * @return string
     */
    public function model(): string
    {
        return Studio::class;
    }

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
            new AllowedInclude(AnimeSchema::class, Studio::RELATION_ANIME),
            new AllowedInclude(ExternalResourceSchema::class, Studio::RELATION_RESOURCES),

            // Undocumented paths needed for client builds
            new AllowedInclude(ImageSchema::class, 'anime.images'),
            new AllowedInclude(VideoSchema::class, 'anime.animethemes.animethemeentries.videos'),
            new AllowedInclude(SongSchema::class, 'anime.animethemes.song'),
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
                new IdField(Studio::ATTRIBUTE_ID),
                new StudioNameField(),
                new StudioSlugField(),
                new StudioAsField(),
            ],
        );
    }
}
