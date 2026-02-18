<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Contracts\Http\Api\Schema\InteractsWithPivots;
use App\Contracts\Http\Api\Schema\SearchableSchema;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Anime\AnimeMediaFormatField;
use App\Http\Api\Field\Wiki\Anime\AnimeNameField;
use App\Http\Api\Field\Wiki\Anime\AnimeSeasonField;
use App\Http\Api\Field\Wiki\Anime\AnimeSlugField;
use App\Http\Api\Field\Wiki\Anime\AnimeSynopsisField;
use App\Http\Api\Field\Wiki\Anime\AnimeYearField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Auth\UserSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Pivot\Morph\ResourceableSchema;
use App\Http\Api\Schema\Wiki\Anime\AnimeSynonymSchema;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Api\Schema\Wiki\Video\ScriptSchema;
use App\Http\Resources\Wiki\Resource\AnimeJsonResource;
use App\Models\Wiki\Anime;

class AnimeSchema extends EloquentSchema implements InteractsWithPivots, SearchableSchema
{
    /**
     * @return AllowedInclude[]
     */
    public function allowedPivots(): array
    {
        return [
            new AllowedInclude(new ResourceableSchema($this, 'animeresource'), 'animeresource'),
        ];
    }

    public function type(): string
    {
        return AnimeJsonResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new ArtistSchema(), Anime::RELATION_ARTISTS),
            new AllowedInclude(new AudioSchema(), Anime::RELATION_AUDIO),
            new AllowedInclude(new EntrySchema(), Anime::RELATION_ENTRIES),
            new AllowedInclude(new ExternalResourceSchema(), Anime::RELATION_RESOURCES),
            new AllowedInclude(new GroupSchema(), Anime::RELATION_GROUPS),
            new AllowedInclude(new ImageSchema(), Anime::RELATION_IMAGES),
            new AllowedInclude(new ScriptSchema(), Anime::RELATION_SCRIPTS),
            new AllowedInclude(new SeriesSchema(), Anime::RELATION_SERIES),
            new AllowedInclude(new SongSchema(), Anime::RELATION_SONG),
            new AllowedInclude(new StudioSchema(), Anime::RELATION_STUDIOS),
            new AllowedInclude(new AnimeSynonymSchema(), Anime::RELATION_ANIMESYNONYMS),
            new AllowedInclude(new SynonymSchema(), Anime::RELATION_SYNONYMS),
            new AllowedInclude(new ThemeSchema(), Anime::RELATION_THEMES),
            new AllowedInclude(new VideoSchema(), Anime::RELATION_VIDEOS),

            // Undocumented paths needed for client builds
            new AllowedInclude(new AnimeSchema(), 'animethemes.animethemeentries.videos.animethemeentries.animetheme.anime'),
            new AllowedInclude(new ImageSchema(), 'animethemes.animethemeentries.videos.animethemeentries.animetheme.anime.images'),
            new AllowedInclude(new VideoSchema(), 'animethemes.animethemeentries.videos.animethemeentries.animetheme.animethemeentries.videos'),
            new AllowedInclude(new AudioSchema(), 'animethemes.animethemeentries.videos.animethemeentries.animetheme.animethemeentries.videos.audio'),
            new AllowedInclude(new ArtistSchema(), 'animethemes.animethemeentries.videos.animethemeentries.animetheme.song.artists'),
            new AllowedInclude(new GroupSchema(), 'animethemes.animethemeentries.videos.animethemeentries.animetheme.group'),
            new AllowedInclude(new ExternalResourceSchema(), 'animethemes.animethemeentries.videos.animethemeentries.animetheme.song.resources'),
            new AllowedInclude(new ExternalResourceSchema(), 'animethemes.song.resources'),
            new AllowedInclude(new ImageSchema(), 'animethemes.song.artists.images'),
            new AllowedInclude(new ImageSchema(), 'studios.images'),
            new AllowedInclude(new UserSchema(), 'animethemes.animethemeentries.videos.tracks.playlist.user'),
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
                new IdField($this, Anime::ATTRIBUTE_ID),
                new AnimeNameField($this),
                new AnimeMediaFormatField($this),
                new AnimeSeasonField($this),
                new AnimeSlugField($this),
                new AnimeSynopsisField($this),
                new AnimeYearField($this),
            ],
        );
    }
}
