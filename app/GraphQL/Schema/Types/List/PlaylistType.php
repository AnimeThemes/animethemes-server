<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\List;

use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\LikesCountField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\List\Playlist\PlaylistDescriptionField;
use App\GraphQL\Schema\Fields\List\Playlist\PlaylistIdField;
use App\GraphQL\Schema\Fields\List\Playlist\PlaylistNameField;
use App\GraphQL\Schema\Fields\List\Playlist\PlaylistTracksCountField;
use App\GraphQL\Schema\Fields\List\Playlist\PlaylistTracksExistsField;
use App\GraphQL\Schema\Fields\List\Playlist\PlaylistVisibilityField;
use App\GraphQL\Schema\Fields\LocalizedEnumField;
use App\GraphQL\Schema\Types\Auth\UserType;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\List\Playlist\PlaylistTrackType;
use App\GraphQL\Schema\Types\Pivot\Morph\ImageableType;
use App\GraphQL\Schema\Types\Wiki\ImageType;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\HasManyRelation;
use App\GraphQL\Support\Relations\MorphToManyRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\List\Playlist;

class PlaylistType extends EloquentType
{
    public function description(): string
    {
        return "Represents a list of ordered tracks intended for continuous playback.\n\nFor example, a \"/r/anime's Best OPs and EDs of 2022\" playlist may contain a collection of tracks allowing the continuous playback of Best OP and ED nominations for the /r/anime Awards.";
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new PlaylistTrackType(), Playlist::RELATION_FIRST),
            new BelongsToRelation(new PlaylistTrackType(), Playlist::RELATION_LAST),
            new BelongsToRelation(new UserType(), Playlist::RELATION_USER)
                ->notNullable(),
            new HasManyRelation(new PlaylistTrackType(), Playlist::RELATION_TRACKS),
            new MorphToManyRelation($this, ImageType::class, Playlist::RELATION_IMAGES, ImageableType::class),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new PlaylistIdField(),
            new PlaylistNameField(),
            new PlaylistDescriptionField(),
            new PlaylistVisibilityField(),
            new LocalizedEnumField(new PlaylistVisibilityField()),
            new PlaylistTracksCountField(),
            new PlaylistTracksExistsField(),
            new LikesCountField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
