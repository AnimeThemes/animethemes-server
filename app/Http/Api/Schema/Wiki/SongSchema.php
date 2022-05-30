<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Song\SongAsField;
use App\Http\Api\Field\Wiki\Song\SongTitleField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Resources\Wiki\Resource\SongResource;
use App\Models\Wiki\Song;

/**
 * Class SongSchema.
 */
class SongSchema extends EloquentSchema
{
    /**
     * The model this schema represents.
     *
     * @return string
     */
    public function model(): string
    {
        return Song::class;
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
        return [
            new AllowedInclude(new AnimeSchema(), Song::RELATION_ANIME),
            new AllowedInclude(new ArtistSchema(), Song::RELATION_ARTISTS),
            new AllowedInclude(new ThemeSchema(), Song::RELATION_ANIMETHEMES),
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
                new IdField(Song::ATTRIBUTE_ID),
                new SongTitleField(),
                new SongAsField(),
            ],
        );
    }
}
