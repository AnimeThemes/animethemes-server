<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\List\Playlist;

use App\Http\Api\Field\Field;
use App\Http\Api\Field\List\Playlist\Track\TrackEntryIdField;
use App\Http\Api\Field\List\Playlist\Track\TrackHashidsField;
use App\Http\Api\Field\List\Playlist\Track\TrackIdField;
use App\Http\Api\Field\List\Playlist\Track\TrackNextHashidsField;
use App\Http\Api\Field\List\Playlist\Track\TrackNextIdField;
use App\Http\Api\Field\List\Playlist\Track\TrackPlaylistIdField;
use App\Http\Api\Field\List\Playlist\Track\TrackPreviousHashidsField;
use App\Http\Api\Field\List\Playlist\Track\TrackPreviousIdField;
use App\Http\Api\Field\List\Playlist\Track\TrackVideoIdField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\List\PlaylistSchema;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Api\Schema\Wiki\AudioSchema;
use App\Http\Api\Schema\Wiki\GroupSchema;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Api\Schema\Wiki\VideoSchema;
use App\Http\Resources\List\Playlist\Resource\TrackResource;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrackSchema.
 */
class TrackSchema extends EloquentSchema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return TrackResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return array_merge(
            $this->withIntermediatePaths([
                new AllowedInclude(new ArtistSchema(), PlaylistTrack::RELATION_ARTISTS),
                new AllowedInclude(new AudioSchema(), PlaylistTrack::RELATION_AUDIO),
                new AllowedInclude(new EntrySchema(), PlaylistTrack::RELATION_ENTRY),
                new AllowedInclude(new GroupSchema(), PlaylistTrack::RELATION_THEME_GROUP),
                new AllowedInclude(new ImageSchema(), PlaylistTrack::RELATION_IMAGES),
                new AllowedInclude(new PlaylistSchema(), PlaylistTrack::RELATION_PLAYLIST),
                new AllowedInclude(new TrackSchema(), PlaylistTrack::RELATION_NEXT),
                new AllowedInclude(new TrackSchema(), PlaylistTrack::RELATION_PREVIOUS),
                new AllowedInclude(new VideoSchema(), PlaylistTrack::RELATION_VIDEO),
            ]),
            []
        );
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
                new TrackIdField($this),
                new TrackHashidsField($this),
                new TrackNextIdField($this),
                new TrackNextHashidsField($this),
                new TrackPlaylistIdField($this),
                new TrackPreviousIdField($this),
                new TrackPreviousHashidsField($this),
                new TrackEntryIdField($this),
                new TrackVideoIdField($this),
            ],
        );
    }

    /**
     * Get the model of the schema.
     *
     * @return Model
     */
    public function model(): Model
    {
        return new PlaylistTrack();
    }
}
