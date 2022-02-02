<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Enums\Http\Api\Field\Category;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\IntField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\SongResource;
use App\Models\Wiki\Song;
use App\Pivots\ArtistSong;

/**
 * Class SongSchema.
 */
class SongSchema extends Schema
{
    /**
     * The model this schema represents.
     *
     * @var string|null
     */
    public static ?string $model = Song::class;

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
            AllowedInclude::make(AnimeSchema::class, Song::RELATION_ANIME),
            AllowedInclude::make(ArtistSchema::class, Song::RELATION_ARTISTS),
            AllowedInclude::make(ThemeSchema::class, Song::RELATION_ANIMETHEMES),
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
                new IntField(BaseResource::ATTRIBUTE_ID, Song::ATTRIBUTE_ID),
                new StringField(Song::ATTRIBUTE_TITLE),
                new StringField(ArtistSong::ATTRIBUTE_AS, null, Category::COMPUTED()),
            ],
        );
    }
}
