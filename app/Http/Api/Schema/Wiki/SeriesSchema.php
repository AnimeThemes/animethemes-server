<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Contracts\Http\Api\Schema\SearchableSchema;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Series\SeriesNameField;
use App\Http\Api\Field\Wiki\Series\SeriesSlugField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Resources\Wiki\Resource\SeriesResource;
use App\Models\Wiki\Series;

/**
 * Class SeriesSchema.
 */
class SeriesSchema extends EloquentSchema implements SearchableSchema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return SeriesResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [
            new AllowedInclude(new AnimeSchema(), Series::RELATION_ANIME),

            // Undocumented paths needed for client builds
            new AllowedInclude(new ImageSchema(), 'anime.images'),
            new AllowedInclude(new VideoSchema(), 'anime.animethemes.animethemeentries.videos'),
            new AllowedInclude(new GroupSchema(), 'anime.animethemes.theme_group'),
            new AllowedInclude(new SongSchema(), 'anime.animethemes.song'),
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
                new IdField($this, Series::ATTRIBUTE_ID),
                new SeriesNameField($this),
                new SeriesSlugField($this),
            ],
        );
    }
}
