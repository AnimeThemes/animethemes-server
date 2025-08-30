<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Auth\User;

use App\GraphQL\Definition\Fields\Auth\User\Me\MeEmailField;
use App\GraphQL\Definition\Fields\Auth\User\Me\MeEmailVerifiedAtField;
use App\GraphQL\Definition\Fields\Auth\User\Me\MeNameField;
use App\GraphQL\Definition\Fields\Auth\User\Me\MeTwoFactorConfirmedAtField;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\Auth\PermissionType;
use App\GraphQL\Definition\Types\Auth\RoleType;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\List\PlaylistType;
use App\GraphQL\Definition\Types\Wiki\VideoType;
use App\GraphQL\Definition\Unions\NotificationUnion;
use App\GraphQL\Support\Relations\BelongsToManyRelation;
use App\GraphQL\Support\Relations\HasManyRelation;
use App\GraphQL\Support\Relations\MorphManyRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\Auth\User;

class MeType extends EloquentType
{
    public function description(): string
    {
        return 'Represents the currently authenticated user.';
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new MorphManyRelation(new NotificationUnion(), User::RELATION_NOTIFICATIONS),
            new HasManyRelation(new PlaylistType(), User::RELATION_PLAYLISTS),
            new BelongsToManyRelation($this, RoleType::class, User::RELATION_ROLES),
            new BelongsToManyRelation($this, PermissionType::class, User::RELATION_PERMISSIONS),
            new BelongsToManyRelation($this, PlaylistType::class, 'likedplaylists'),
            new BelongsToManyRelation($this, VideoType::class, 'likedvideos'),
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
            new MeNameField(),
            new MeEmailField(),
            new MeEmailVerifiedAtField(),
            new MeTwoFactorConfirmedAtField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }

    /**
     * Get the model string representation for the type.
     *
     * @return class-string<User>
     */
    public function model(): string
    {
        return User::class;
    }
}
