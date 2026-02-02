<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Contracts\Http\Api\Schema\InteractsWithPivots;
use App\Contracts\Http\Api\Schema\SearchableSchema;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Studio\StudioNameField;
use App\Http\Api\Field\Wiki\Studio\StudioSlugField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Pivot\Morph\ResourceableSchema;
use App\Http\Resources\Wiki\Resource\StudioJsonResource;
use App\Models\Wiki\Studio;

class StudioSchema extends EloquentSchema implements InteractsWithPivots, SearchableSchema
{
    /**
     * @return AllowedInclude[]
     */
    public function allowedPivots(): array
    {
        return [
            new AllowedInclude(new ResourceableSchema($this, 'studioresource'), 'studioresource'),
        ];
    }

    public function type(): string
    {
        return StudioJsonResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new AnimeSchema(), Studio::RELATION_ANIME),
            new AllowedInclude(new ExternalResourceSchema(), Studio::RELATION_RESOURCES),
            new AllowedInclude(new ImageSchema(), Studio::RELATION_IMAGES),

            // Undocumented paths needed for client builds
            new AllowedInclude(new ImageSchema(), 'anime.images'),
            new AllowedInclude(new VideoSchema(), 'anime.animethemes.animethemeentries.videos'),
            new AllowedInclude(new SongSchema(), 'anime.animethemes.song'),
            new AllowedInclude(new GroupSchema(), 'anime.animethemes.group'),
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
                new IdField($this, Studio::ATTRIBUTE_ID),
                new StudioNameField($this),
                new StudioSlugField($this),
            ],
        );
    }
}
