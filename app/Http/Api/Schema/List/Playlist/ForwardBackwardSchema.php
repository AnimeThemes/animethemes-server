<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\List\Playlist;

use App\Http\Api\Field\Field;
use App\Http\Api\Field\List\Playlist\Track\TrackEntryIdField;
use App\Http\Api\Field\List\Playlist\Track\TrackHashidsField;
use App\Http\Api\Field\List\Playlist\Track\TrackIdField;
use App\Http\Api\Field\List\Playlist\Track\TrackVideoIdField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Api\Schema\Wiki\AudioSchema;
use App\Http\Api\Schema\Wiki\GroupSchema;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Api\Schema\Wiki\VideoSchema;
use App\Http\Resources\List\Playlist\Resource\TrackResource;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ForwardBackwardSchema.
 */
class ForwardBackwardSchema extends EloquentSchema
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
                new AllowedInclude(new GroupSchema(), PlaylistTrack::RELATION_THEME_GROUP),
                new AllowedInclude(new ImageSchema(), PlaylistTrack::RELATION_IMAGES),
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
