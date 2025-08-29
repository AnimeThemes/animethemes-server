<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Schema\List;

use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Auth\UserSchema;
use App\Http\Api\Schema\List\Playlist\TrackSchema;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Resources\List\Resource\PlaylistResource;
use App\Models\List\Playlist;
use App\Scout\Elasticsearch\Api\Field\Base\CreatedAtField;
use App\Scout\Elasticsearch\Api\Field\Base\UpdatedAtField;
use App\Scout\Elasticsearch\Api\Field\Field;
use App\Scout\Elasticsearch\Api\Field\List\Playlist\PlaylistDescriptionField;
use App\Scout\Elasticsearch\Api\Field\List\Playlist\PlaylistHashidsField;
use App\Scout\Elasticsearch\Api\Field\List\Playlist\PlaylistNameField;
use App\Scout\Elasticsearch\Api\Field\List\Playlist\PlaylistVisibilityField;
use App\Scout\Elasticsearch\Api\Query\List\PlaylistQuery;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class PlaylistSchema extends Schema
{
    /**
     * The model this schema represents.
     */
    public function query(): PlaylistQuery
    {
        return new PlaylistQuery();
    }

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
            new PlaylistHashidsField($this),
            new PlaylistNameField($this),
            new PlaylistDescriptionField($this),
            new PlaylistVisibilityField($this),
        ];
    }
}
