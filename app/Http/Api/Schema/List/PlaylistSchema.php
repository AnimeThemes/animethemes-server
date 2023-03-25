<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\List;

use App\Contracts\Http\Api\Schema\SearchableSchema;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\List\Playlist\PlaylistFirstIdField;
use App\Http\Api\Field\List\Playlist\PlaylistHashidsField;
use App\Http\Api\Field\List\Playlist\PlaylistLastIdField;
use App\Http\Api\Field\List\Playlist\PlaylistNameField;
use App\Http\Api\Field\List\Playlist\PlaylistTrackCountField;
use App\Http\Api\Field\List\Playlist\PlaylistTrackExistsField;
use App\Http\Api\Field\List\Playlist\PlaylistUserIdField;
use App\Http\Api\Field\List\Playlist\PlaylistViewCountField;
use App\Http\Api\Field\List\Playlist\PlaylistVisibilityField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Auth\UserSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\List\Playlist\TrackSchema;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Resources\List\Resource\PlaylistResource;
use App\Models\List\Playlist;

/**
 * Class PlaylistSchema.
 */
class PlaylistSchema extends EloquentSchema implements SearchableSchema
{
    /**
     * The model this schema represents.
     *
     * @return string
     */
    public function model(): string
    {
        return Playlist::class;
    }

    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return PlaylistResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [
            new AllowedInclude(new ImageSchema(), Playlist::RELATION_IMAGES),
            new AllowedInclude(new TrackSchema(), Playlist::RELATION_FIRST),
            new AllowedInclude(new TrackSchema(), Playlist::RELATION_LAST),
            new AllowedInclude(new TrackSchema(), Playlist::RELATION_TRACKS),
            new AllowedInclude(new UserSchema(), Playlist::RELATION_USER),
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
                new IdField($this, Playlist::ATTRIBUTE_ID), // TODO custom id field to prevent rendering
                new PlaylistFirstIdField($this),
                new PlaylistLastIdField($this),
                new PlaylistNameField($this),
                new PlaylistUserIdField($this),
                new PlaylistVisibilityField($this),
                new PlaylistViewCountField($this),
                new PlaylistTrackExistsField($this),
                new PlaylistTrackCountField($this),
                new PlaylistHashidsField($this),
            ],
        );
    }
}
