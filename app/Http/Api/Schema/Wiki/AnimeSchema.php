<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Contracts\Http\Api\Schema\InteractsWithPivots;
use App\Contracts\Http\Api\Schema\SearchableSchema;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Anime\AnimeNameField;
use App\Http\Api\Field\Wiki\Anime\AnimeSeasonField;
use App\Http\Api\Field\Wiki\Anime\AnimeSlugField;
use App\Http\Api\Field\Wiki\Anime\AnimeSynopsisField;
use App\Http\Api\Field\Wiki\Anime\AnimeYearField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Pivot\Wiki\AnimeResourceSchema;
use App\Http\Api\Schema\Wiki\Anime\SynonymSchema;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Api\Schema\Wiki\Video\ScriptSchema;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeResourceResource;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\Wiki\Anime;

/**
 * Class AnimeSchema.
 */
class AnimeSchema extends EloquentSchema implements InteractsWithPivots, SearchableSchema
{
    /**
     * Get the allowed pivots of the schema.
     *
     * @return AllowedInclude[]
     */
    public function allowedPivots(): array
    {
        return [
            new AllowedInclude(new AnimeResourceSchema(), AnimeResourceResource::$wrap),
        ];
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
            new AllowedInclude(new ArtistSchema(), Anime::RELATION_ARTISTS),
            new AllowedInclude(new AudioSchema(), Anime::RELATION_AUDIO),
            new AllowedInclude(new EntrySchema(), Anime::RELATION_ENTRIES),
            new AllowedInclude(new ExternalResourceSchema(), Anime::RELATION_RESOURCES),
            new AllowedInclude(new ImageSchema(), Anime::RELATION_IMAGES),
            new AllowedInclude(new ScriptSchema(), Anime::RELATION_SCRIPTS),
            new AllowedInclude(new SeriesSchema(), Anime::RELATION_SERIES),
            new AllowedInclude(new SongSchema(), Anime::RELATION_SONG),
            new AllowedInclude(new StudioSchema(), Anime::RELATION_STUDIOS),
            new AllowedInclude(new SynonymSchema(), Anime::RELATION_SYNONYMS),
            new AllowedInclude(new ThemeSchema(), Anime::RELATION_THEMES),
            new AllowedInclude(new VideoSchema(), Anime::RELATION_VIDEOS),

            // Undocumented paths needed for client builds
            new AllowedInclude(new AnimeSchema(), 'animethemes.animethemeentries.videos.animethemeentries.animetheme.anime'),
            new AllowedInclude(new ImageSchema(), 'animethemes.animethemeentries.videos.animethemeentries.animetheme.anime.images'),
            new AllowedInclude(new VideoSchema(), 'animethemes.animethemeentries.videos.animethemeentries.animetheme.animethemeentries.videos'),
            new AllowedInclude(new ArtistSchema(), 'animethemes.animethemeentries.videos.animethemeentries.animetheme.song.artists'),
            new AllowedInclude(new ImageSchema(), 'animethemes.song.artists.images'),
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
                new IdField($this, Anime::ATTRIBUTE_ID),
                new AnimeNameField($this),
                new AnimeSeasonField($this),
                new AnimeSlugField($this),
                new AnimeSynopsisField($this),
                new AnimeYearField($this),
            ],
        );
    }
}
