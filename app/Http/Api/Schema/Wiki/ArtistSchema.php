<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Artist\ArtistAsField;
use App\Http\Api\Field\Wiki\Artist\ArtistNameField;
use App\Http\Api\Field\Wiki\Artist\ArtistSlugField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Resources\Wiki\Resource\ArtistResource;
use App\Models\Wiki\Artist;

/**
 * Class ArtistSchema.
 */
class ArtistSchema extends EloquentSchema
{
    /**
     * The model this schema represents.
     *
     * @return string
     */
    public function model(): string
    {
        return Artist::class;
    }

    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return ArtistResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [
            AllowedInclude::make(AnimeSchema::class, Artist::RELATION_ANIME),
            AllowedInclude::make(ArtistSchema::class, Artist::RELATION_GROUPS),
            AllowedInclude::make(ArtistSchema::class, Artist::RELATION_MEMBERS),
            AllowedInclude::make(ExternalResourceSchema::class, Artist::RELATION_RESOURCES),
            AllowedInclude::make(ImageSchema::class, Artist::RELATION_IMAGES),
            AllowedInclude::make(SongSchema::class, Artist::RELATION_SONGS),
            AllowedInclude::make(ThemeSchema::class, Artist::RELATION_ANIMETHEMES),

            // Undocumented paths needed for client builds
            AllowedInclude::make(ArtistSchema::class, 'songs.artists'),
            AllowedInclude::make(SongSchema::class, 'songs.animethemes.song'),
            AllowedInclude::make(ArtistSchema::class, 'songs.animethemes.song.artists'),
            AllowedInclude::make(ImageSchema::class, 'songs.animethemes.anime.images'),
            AllowedInclude::make(EntrySchema::class, 'songs.animethemes.animethemeentries'),
            AllowedInclude::make(VideoSchema::class, 'songs.animethemes.animethemeentries.videos'),
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
                new IdField(Artist::ATTRIBUTE_ID),
                new ArtistNameField(),
                new ArtistSlugField(),
                new ArtistAsField(),
            ],
        );
    }
}
