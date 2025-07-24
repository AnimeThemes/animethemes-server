<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\List;

use App\Contracts\GraphQL\HasFields;
use App\Contracts\GraphQL\HasRelations;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\List\Playlist\PlaylistDescriptionField;
use App\GraphQL\Definition\Fields\List\Playlist\PlaylistIdField;
use App\GraphQL\Definition\Fields\List\Playlist\PlaylistLikesCountField;
use App\GraphQL\Definition\Fields\List\Playlist\PlaylistNameField;
use App\GraphQL\Definition\Fields\List\Playlist\PlaylistTracksCountField;
use App\GraphQL\Definition\Fields\List\Playlist\PlaylistTracksExistsField;
use App\GraphQL\Definition\Fields\List\Playlist\PlaylistVisibilityField;
use App\GraphQL\Definition\Fields\LocalizedEnumField;
use App\GraphQL\Definition\Relations\BelongsToManyRelation;
use App\GraphQL\Definition\Relations\BelongsToRelation;
use App\GraphQL\Definition\Relations\HasManyRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\Auth\UserType;
use App\GraphQL\Definition\Types\Edges\Wiki\ImageEdgeType;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\List\Playlist\PlaylistTrackType;
use App\Models\List\Playlist;

class PlaylistType extends EloquentType implements HasFields, HasRelations
{
    /**
     * The description of the type.
     */
    public function getDescription(): string
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
            new BelongsToRelation(new UserType(), Playlist::RELATION_USER),
            new HasManyRelation(new PlaylistTrackType(), Playlist::RELATION_TRACKS),
            new BelongsToManyRelation(new ImageEdgeType(), Playlist::RELATION_IMAGES),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            new PlaylistIdField(),
            new PlaylistNameField(),
            new PlaylistDescriptionField(),
            new PlaylistVisibilityField(),
            new LocalizedEnumField(new PlaylistVisibilityField()),
            new PlaylistTracksCountField(),
            new PlaylistTracksExistsField(),
            new PlaylistLikesCountField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
