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
            new AllowedInclude(AnimeSchema::class, Artist::RELATION_ANIME),
            new AllowedInclude(ArtistSchema::class, Artist::RELATION_GROUPS),
            new AllowedInclude(ArtistSchema::class, Artist::RELATION_MEMBERS),
            new AllowedInclude(ExternalResourceSchema::class, Artist::RELATION_RESOURCES),
            new AllowedInclude(ImageSchema::class, Artist::RELATION_IMAGES),
            new AllowedInclude(SongSchema::class, Artist::RELATION_SONGS),
            new AllowedInclude(ThemeSchema::class, Artist::RELATION_ANIMETHEMES),

            // Undocumented paths needed for client builds
            new AllowedInclude(ArtistSchema::class, 'songs.artists'),
            new AllowedInclude(SongSchema::class, 'songs.animethemes.song'),
            new AllowedInclude(ArtistSchema::class, 'songs.animethemes.song.artists'),
            new AllowedInclude(ImageSchema::class, 'songs.animethemes.anime.images'),
            new AllowedInclude(EntrySchema::class, 'songs.animethemes.animethemeentries'),
            new AllowedInclude(VideoSchema::class, 'songs.animethemes.animethemeentries.videos'),
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
