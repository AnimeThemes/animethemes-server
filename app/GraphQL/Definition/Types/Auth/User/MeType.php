<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Auth\User;

use App\GraphQL\Definition\Fields\Auth\User\UserEmailField;
use App\GraphQL\Definition\Fields\Auth\User\UserEmailVerifiedAtField;
use App\GraphQL\Definition\Fields\Auth\User\UserNameField;
use App\GraphQL\Definition\Fields\Auth\User\UserTwoFactorConfirmedAtField;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Relations\BelongsToManyRelation;
use App\GraphQL\Definition\Relations\HasManyRelation;
use App\GraphQL\Definition\Relations\MorphManyRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Definition\Types\List\PlaylistType;
use App\GraphQL\Definition\Types\User\NotificationType;
use App\GraphQL\Definition\Types\Wiki\VideoType;
use App\Models\Auth\User;

/**
 * Class MeType.
 */
class MeType extends BaseType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents the currently authenticated user.";
    }

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
     */
    public function relations(): array
    {
        return [
            new MorphManyRelation(new NotificationType(), User::RELATION_NOTIFICATIONS),
            new HasManyRelation(new PlaylistType(), User::RELATION_PLAYLISTS),
            new BelongsToManyRelation(new PlaylistType(), 'likedplaylists'),
            new BelongsToManyRelation(new VideoType(), 'likedvideos'),
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
            new UserEmailField(),
            new UserEmailVerifiedAtField(),
            new UserTwoFactorConfirmedAtField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
