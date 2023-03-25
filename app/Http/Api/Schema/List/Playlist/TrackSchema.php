<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\List\Playlist;

use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\List\Playlist\Track\TrackHashidsField;
use App\Http\Api\Field\List\Playlist\Track\TrackNextIdField;
use App\Http\Api\Field\List\Playlist\Track\TrackPlaylistIdField;
use App\Http\Api\Field\List\Playlist\Track\TrackPreviousIdField;
use App\Http\Api\Field\List\Playlist\Track\TrackVideoIdField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\List\PlaylistSchema;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Api\Schema\Wiki\AudioSchema;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Api\Schema\Wiki\VideoSchema;
use App\Http\Resources\List\Playlist\Resource\TrackResource;
use App\Models\List\Playlist\PlaylistTrack;

/**
 * Class TrackSchema.
 */
class TrackSchema extends EloquentSchema
{
    /**
     * The model this schema represents.
     *
     * @return string
     */
    public function model(): string
    {
        return PlaylistTrack::class;
    }

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
        return [
            new AllowedInclude(new ArtistSchema(), PlaylistTrack::RELATION_ARTISTS),
            new AllowedInclude(new AudioSchema(), PlaylistTrack::RELATION_AUDIO),
            new AllowedInclude(new ImageSchema(), PlaylistTrack::RELATION_IMAGES),
            new AllowedInclude(new PlaylistSchema(), PlaylistTrack::RELATION_PLAYLIST),
            new AllowedInclude(new TrackSchema(), PlaylistTrack::RELATION_NEXT),
            new AllowedInclude(new TrackSchema(), PlaylistTrack::RELATION_PREVIOUS),
            new AllowedInclude(new VideoSchema(), PlaylistTrack::RELATION_VIDEO),
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
                new IdField($this, PlaylistTrack::ATTRIBUTE_ID), // TODO custom id field to prevent rendering
                new TrackNextIdField($this),
                new TrackPlaylistIdField($this),
                new TrackPreviousIdField($this),
                new TrackVideoIdField($this),
                new TrackHashidsField($this),
            ],
        );
    }
}
