<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\List;

use App\Contracts\Http\Api\Schema\SearchableSchema;
use App\Http\Api\Field\Base\CreatedAtField;
use App\Http\Api\Field\Base\UpdatedAtField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\List\Playlist\PlaylistDescriptionField;
use App\Http\Api\Field\List\Playlist\PlaylistFirstIdField;
use App\Http\Api\Field\List\Playlist\PlaylistHashidsField;
use App\Http\Api\Field\List\Playlist\PlaylistIdField;
use App\Http\Api\Field\List\Playlist\PlaylistLastIdField;
use App\Http\Api\Field\List\Playlist\PlaylistNameField;
use App\Http\Api\Field\List\Playlist\PlaylistTrackCountField;
use App\Http\Api\Field\List\Playlist\PlaylistTrackExistsField;
use App\Http\Api\Field\List\Playlist\PlaylistUserIdField;
use App\Http\Api\Field\List\Playlist\PlaylistVisibilityField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Auth\UserSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\List\Playlist\TrackSchema;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Api\Schema\Wiki\AudioSchema;
use App\Http\Api\Schema\Wiki\GroupSchema;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Api\Schema\Wiki\VideoSchema;
use App\Http\Resources\List\Resource\PlaylistResource;
use App\Models\List\Playlist;

class PlaylistSchema extends EloquentSchema implements SearchableSchema
{
    public function type(): string
    {
        return PlaylistResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new ImageSchema(), Playlist::RELATION_IMAGES),
            new AllowedInclude(new TrackSchema(), Playlist::RELATION_FIRST),
            new AllowedInclude(new TrackSchema(), Playlist::RELATION_LAST),
            new AllowedInclude(new TrackSchema(), Playlist::RELATION_TRACKS),
            new AllowedInclude(new UserSchema(), Playlist::RELATION_USER),

            new AllowedInclude(new ArtistSchema(), 'tracks.animethemeentry.animetheme.song.artists'),
            new AllowedInclude(new AudioSchema(), 'tracks.video.audio'),
            new AllowedInclude(new EntrySchema(), 'tracks.animethemeentry'),
            new AllowedInclude(new ImageSchema(), 'tracks.animethemeentry.animetheme.anime.images'),
            new AllowedInclude(new VideoSchema(), 'tracks.video'),
            new AllowedInclude(new GroupSchema(), 'tracks.animethemeentry.animetheme.group'),
            new AllowedInclude(new TrackSchema(), 'tracks.previous'),
            new AllowedInclude(new TrackSchema(), 'tracks.next'),
        ]);
    }

    /**
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            new CreatedAtField($this),
            new UpdatedAtField($this),
            new PlaylistIdField($this),
            new PlaylistHashidsField($this),
            new PlaylistFirstIdField($this),
            new PlaylistLastIdField($this),
            new PlaylistNameField($this),
            new PlaylistDescriptionField($this),
            new PlaylistUserIdField($this),
            new PlaylistVisibilityField($this),
            new PlaylistTrackExistsField($this),
            new PlaylistTrackCountField($this),
        ];
    }
}
