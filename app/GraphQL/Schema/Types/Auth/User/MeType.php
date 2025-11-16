<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Auth\User;

use App\GraphQL\Schema\Fields\Auth\User\Me\MeEmailField;
use App\GraphQL\Schema\Fields\Auth\User\Me\MeEmailVerifiedAtField;
use App\GraphQL\Schema\Fields\Auth\User\Me\MeNameField;
use App\GraphQL\Schema\Fields\Auth\User\Me\MeTwoFactorConfirmedAtField;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\IdField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Types\Auth\PermissionType;
use App\GraphQL\Schema\Types\Auth\RoleType;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\List\PlaylistType;
use App\GraphQL\Schema\Types\Wiki\Anime\Theme\AnimeThemeEntryType;
use App\GraphQL\Schema\Unions\NotificationUnion;
use App\GraphQL\Schema\Relations\BelongsToManyRelation;
use App\GraphQL\Schema\Relations\HasManyRelation;
use App\GraphQL\Schema\Relations\MorphManyRelation;
use App\GraphQL\Schema\Relations\Relation;
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
            new BelongsToManyRelation($this, AnimeThemeEntryType::class, 'likedentries'),
            new BelongsToManyRelation($this, PlaylistType::class, 'likedplaylists'),
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
