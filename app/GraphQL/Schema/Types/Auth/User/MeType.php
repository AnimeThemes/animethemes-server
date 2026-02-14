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
use App\GraphQL\Schema\Fields\Relations\BelongsToManyRelation;
use App\GraphQL\Schema\Fields\Relations\HasManyRelation;
use App\GraphQL\Schema\Fields\Relations\MorphManyRelation;
use App\GraphQL\Schema\Fields\Relations\MorphToManyRelation;
use App\GraphQL\Schema\Types\Auth\PermissionType;
use App\GraphQL\Schema\Types\Auth\RoleType;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\List\PlaylistType;
use App\GraphQL\Schema\Types\User\WatchHistoryType;
use App\GraphQL\Schema\Types\Wiki\Anime\Theme\AnimeThemeEntryType;
use App\GraphQL\Schema\Unions\NotificationUnion;
use App\Models\Auth\User;

class MeType extends EloquentType
{
    public function description(): string
    {
        return 'Represents the currently authenticated user.';
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
            new CreatedAtField(false),
            new UpdatedAtField(false),

            new MorphManyRelation(new NotificationUnion(), User::RELATION_NOTIFICATIONS),
            new HasManyRelation(new PlaylistType(), User::RELATION_PLAYLISTS),
            new MorphToManyRelation($this, new RoleType(), User::RELATION_ROLES),
            new MorphToManyRelation($this, new PermissionType(), User::RELATION_PERMISSIONS),
            new BelongsToManyRelation($this, new AnimeThemeEntryType(), 'likedentries'),
            new BelongsToManyRelation($this, new PlaylistType(), 'likedplaylists'),
            new HasManyRelation(new WatchHistoryType(), User::RELATION_WATCH_HISTORY),
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

    public function hasSortableColumns(): bool
    {
        return false;
    }

    public function hasFilterableColumns(): bool
    {
        return false;
    }
}
