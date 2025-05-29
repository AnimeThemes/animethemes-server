<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Auth;

use App\GraphQL\Definition\Fields\Auth\User\UserNameField;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Relations\HasManyRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\List\PlaylistType;
use App\Models\Auth\User;

/**
 * Class UserType.
 */
class UserType extends EloquentType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents an AnimeThemes account.";
    }

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
     */
    public function relations(): array
    {
        return [
            new HasManyRelation(new PlaylistType(), User::RELATION_PLAYLISTS),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            new IdField(User::ATTRIBUTE_ID),
            new UserNameField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
