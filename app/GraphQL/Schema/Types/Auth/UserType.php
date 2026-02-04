<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Auth;

use App\GraphQL\Schema\Fields\Auth\User\UserNameField;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\IdField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Relations\HasManyRelation;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\List\PlaylistType;
use App\Models\Auth\User;

class UserType extends EloquentType
{
    public function description(): string
    {
        return 'Represents an AnimeThemes account.';
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

            new HasManyRelation(new PlaylistType(), User::RELATION_PLAYLISTS),
        ];
    }
}
