<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Contracts\Http\Api\Schema\InteractsWithPivots;
use App\Contracts\Http\Api\Schema\SearchableSchema;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Artist\ArtistInformationField;
use App\Http\Api\Field\Wiki\Artist\ArtistNameField;
use App\Http\Api\Field\Wiki\Artist\ArtistSlugField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Pivot\Wiki\ArtistImageSchema;
use App\Http\Api\Schema\Pivot\Wiki\ArtistMemberSchema;
use App\Http\Api\Schema\Pivot\Wiki\ArtistResourceSchema;
use App\Http\Api\Schema\Pivot\Wiki\ArtistSongSchema;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistImageResource;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistMemberResource;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistResourceResource;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistSongResource;
use App\Http\Resources\Wiki\Resource\ArtistResource;
use App\Models\Wiki\Artist;

class ArtistSchema extends EloquentSchema implements InteractsWithPivots, SearchableSchema
{
    /**
     * Get the allowed pivots of the schema.
     *
     * @return AllowedInclude[]
     */
    public function allowedPivots(): array
    {
        return [
            new AllowedInclude(new ArtistImageSchema(), ArtistImageResource::$wrap),
            new AllowedInclude(new ArtistMemberSchema(), ArtistMemberResource::$wrap),
            new AllowedInclude(new ArtistResourceSchema(), ArtistResourceResource::$wrap),
            new AllowedInclude(new ArtistSongSchema(), ArtistSongResource::$wrap),
        ];
    }

    /**
     * Get the type of the resource.
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
        return $this->withIntermediatePaths([
            new AllowedInclude(new AnimeSchema(), Artist::RELATION_ANIME),
            new AllowedInclude(new ArtistSchema(), Artist::RELATION_GROUPS),
            new AllowedInclude(new ArtistSchema(), Artist::RELATION_MEMBERS),
            new AllowedInclude(new ExternalResourceSchema(), Artist::RELATION_RESOURCES),
            new AllowedInclude(new GroupSchema(), Artist::RELATION_THEME_GROUPS),
            new AllowedInclude(new ImageSchema(), Artist::RELATION_IMAGES),
            new AllowedInclude(new SongSchema(), Artist::RELATION_SONGS),
            new AllowedInclude(new ThemeSchema(), Artist::RELATION_ANIMETHEMES),

            // Undocumented paths needed for client builds
            new AllowedInclude(new ArtistSchema(), 'groups.songs.artists'),
            new AllowedInclude(new SongSchema(), 'groups.songs.animethemes.song'),
            new AllowedInclude(new GroupSchema(), 'groups.songs.animethemes.group'),
            new AllowedInclude(new ArtistSchema(), 'groups.songs.animethemes.song.artists'),
            new AllowedInclude(new ImageSchema(), 'groups.songs.animethemes.anime.images'),
            new AllowedInclude(new EntrySchema(), 'groups.songs.animethemes.animethemeentries'),
            new AllowedInclude(new VideoSchema(), 'groups.songs.animethemes.animethemeentries.videos'),
            new AllowedInclude(new AudioSchema(), 'groups.songs.animethemes.animethemeentries.videos.audio'),
            new AllowedInclude(new ExternalResourceSchema(), 'groups.songs.resources'),
            new AllowedInclude(new ArtistSchema(), 'songs.artists'),
            new AllowedInclude(new SongSchema(), 'songs.animethemes.song'),
            new AllowedInclude(new ArtistSchema(), 'songs.animethemes.song.artists'),
            new AllowedInclude(new ImageSchema(), 'songs.animethemes.anime.images'),
            new AllowedInclude(new EntrySchema(), 'songs.animethemes.animethemeentries'),
            new AllowedInclude(new VideoSchema(), 'songs.animethemes.animethemeentries.videos'),
            new AllowedInclude(new AudioSchema(), 'songs.animethemes.animethemeentries.videos.audio'),
            new AllowedInclude(new ExternalResourceSchema(), 'songs.resources'),
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
                new IdField($this, Artist::ATTRIBUTE_ID),
                new ArtistNameField($this),
                new ArtistSlugField($this),
                new ArtistInformationField($this),
            ],
        );
    }
}
