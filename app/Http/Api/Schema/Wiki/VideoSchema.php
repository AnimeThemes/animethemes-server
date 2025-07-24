<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Contracts\Http\Api\Schema\SearchableSchema;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Video\VideoAudioIdField;
use App\Http\Api\Field\Wiki\Video\VideoBasenameField;
use App\Http\Api\Field\Wiki\Video\VideoFilenameField;
use App\Http\Api\Field\Wiki\Video\VideoLinkField;
use App\Http\Api\Field\Wiki\Video\VideoLyricsField;
use App\Http\Api\Field\Wiki\Video\VideoMimeTypeField;
use App\Http\Api\Field\Wiki\Video\VideoNcField;
use App\Http\Api\Field\Wiki\Video\VideoOverlapField;
use App\Http\Api\Field\Wiki\Video\VideoPathField;
use App\Http\Api\Field\Wiki\Video\VideoResolutionField;
use App\Http\Api\Field\Wiki\Video\VideoSizeField;
use App\Http\Api\Field\Wiki\Video\VideoSourceField;
use App\Http\Api\Field\Wiki\Video\VideoSubbedField;
use App\Http\Api\Field\Wiki\Video\VideoTagsField;
use App\Http\Api\Field\Wiki\Video\VideoUncenField;
use App\Http\Api\Field\Wiki\Video\VideoViewCountField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\List\Playlist\TrackSchema;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Api\Schema\Wiki\Video\ScriptSchema;
use App\Http\Resources\Wiki\Resource\VideoResource;
use App\Models\Wiki\Video;

class VideoSchema extends EloquentSchema implements SearchableSchema
{
    /**
     * Get the type of the resource.
     */
    public function type(): string
    {
        return VideoResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new AnimeSchema(), Video::RELATION_ANIME),
            new AllowedInclude(new AudioSchema(), Video::RELATION_AUDIO),
            new AllowedInclude(new EntrySchema(), Video::RELATION_ANIMETHEMEENTRIES),
            new AllowedInclude(new GroupSchema(), Video::RELATION_GROUP),
            new AllowedInclude(new ScriptSchema(), Video::RELATION_SCRIPT),
            new AllowedInclude(new ThemeSchema(), Video::RELATION_ANIMETHEME),
            new AllowedInclude(new TrackSchema(), Video::RELATION_TRACKS),

            // Undocumented paths needed for client builds
            new AllowedInclude(new SongSchema(), 'animethemeentries.animetheme.song'),
            new AllowedInclude(new ArtistSchema(), 'animethemeentries.animetheme.song.artists'),
            new AllowedInclude(new ImageSchema(), 'animethemeentries.animetheme.anime.images'),
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
                new IdField($this, Video::ATTRIBUTE_ID),
                new VideoAudioIdField($this),
                new VideoBasenameField($this),
                new VideoFilenameField($this),
                new VideoLyricsField($this),
                new VideoMimeTypeField($this),
                new VideoNcField($this),
                new VideoOverlapField($this),
                new VideoPathField($this),
                new VideoResolutionField($this),
                new VideoSizeField($this),
                new VideoSourceField($this),
                new VideoSubbedField($this),
                new VideoUncenField($this),
                new VideoTagsField($this),
                new VideoLinkField($this),
                new VideoViewCountField($this),
            ],
        );
    }
}
