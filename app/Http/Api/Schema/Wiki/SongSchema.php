<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Contracts\Http\Api\Schema\InteractsWithPivots;
use App\Contracts\Http\Api\Schema\SearchableSchema;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Song\SongTitleField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Pivot\Wiki\ArtistSongSchema;
use App\Http\Api\Schema\Pivot\Wiki\SongResourceSchema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Api\Schema\Wiki\Song\PerformanceSchema;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistSongResource;
use App\Http\Resources\Pivot\Wiki\Resource\SongResourceResource;
use App\Http\Resources\Wiki\Resource\SongResource;
use App\Models\Wiki\Song;

/**
 * Class SongSchema.
 */
class SongSchema extends EloquentSchema implements InteractsWithPivots, SearchableSchema
{
    /**
     * Get the allowed pivots of the schema.
     *
     * @return AllowedInclude[]
     */
    public function allowedPivots(): array
    {
        return [
            new AllowedInclude(new ArtistSongSchema(), ArtistSongResource::$wrap),
            new AllowedInclude(new SongResourceSchema(), SongResourceResource::$wrap)
        ];
    }

    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return SongResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new AnimeSchema(), Song::RELATION_ANIME),
            new AllowedInclude(new ArtistSchema(), Song::RELATION_ARTISTS),
            new AllowedInclude(new ExternalResourceSchema(), Song::RELATION_RESOURCES),
            new AllowedInclude(new GroupSchema(), Song::RELATION_THEME_GROUPS),
            new AllowedInclude(new PerformanceSchema(), Song::RELATION_PERFORMANCES),
            new AllowedInclude(new ThemeSchema(), Song::RELATION_ANIMETHEMES),
        ]);
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
                new IdField($this, Song::ATTRIBUTE_ID),
                new SongTitleField($this),
            ],
        );
    }
}
