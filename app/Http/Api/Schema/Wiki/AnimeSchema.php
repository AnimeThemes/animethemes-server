<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Anime\AnimeAsField;
use App\Http\Api\Field\Wiki\Anime\AnimeNameField;
use App\Http\Api\Field\Wiki\Anime\AnimeSeasonField;
use App\Http\Api\Field\Wiki\Anime\AnimeSlugField;
use App\Http\Api\Field\Wiki\Anime\AnimeSynopsisField;
use App\Http\Api\Field\Wiki\Anime\AnimeYearField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\Anime\SynonymSchema;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\Wiki\Anime;

/**
 * Class AnimeSchema.
 */
class AnimeSchema extends EloquentSchema
{
    /**
     * The model this schema represents.
     *
     * @return string
     */
    public function model(): string
    {
        return Anime::class;
    }

    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return AnimeResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [
            new AllowedInclude(ArtistSchema::class, Anime::RELATION_ARTISTS),
            new AllowedInclude(EntrySchema::class, Anime::RELATION_ENTRIES),
            new AllowedInclude(ExternalResourceSchema::class, Anime::RELATION_RESOURCES),
            new AllowedInclude(ImageSchema::class, Anime::RELATION_IMAGES),
            new AllowedInclude(SeriesSchema::class, Anime::RELATION_SERIES),
            new AllowedInclude(SongSchema::class, Anime::RELATION_SONG),
            new AllowedInclude(StudioSchema::class, Anime::RELATION_STUDIOS),
            new AllowedInclude(SynonymSchema::class, Anime::RELATION_SYNONYMS),
            new AllowedInclude(ThemeSchema::class, Anime::RELATION_THEMES),
            new AllowedInclude(VideoSchema::class, Anime::RELATION_VIDEOS),

            // Undocumented paths needed for client builds
            new AllowedInclude(AnimeSchema::class, 'animethemes.animethemeentries.videos.animethemeentries.animetheme.anime'),
            new AllowedInclude(ImageSchema::class, 'animethemes.animethemeentries.videos.animethemeentries.animetheme.anime.images'),
            new AllowedInclude(VideoSchema::class, 'animethemes.animethemeentries.videos.animethemeentries.animetheme.animethemeentries.videos'),
            new AllowedInclude(ArtistSchema::class, 'animethemes.animethemeentries.videos.animethemeentries.animetheme.song.artists'),
            new AllowedInclude(ImageSchema::class, 'animethemes.song.artists.images'),
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
                new IdField(Anime::ATTRIBUTE_ID),
                new AnimeNameField(),
                new AnimeSeasonField(),
                new AnimeSlugField(),
                new AnimeSynopsisField(),
                new AnimeYearField(),
                new AnimeAsField(),
            ],
        );
    }
}
