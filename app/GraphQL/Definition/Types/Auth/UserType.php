<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Auth;

use App\GraphQL\Definition\Fields\Auth\User\UserNameField;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\List\PlaylistType;
use App\GraphQL\Support\Relations\HasManyRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\Auth\User;

class UserType extends EloquentType
{
    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Represents an AnimeThemes account.';
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
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
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new IdField(User::ATTRIBUTE_ID, User::class),
            new UserNameField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
