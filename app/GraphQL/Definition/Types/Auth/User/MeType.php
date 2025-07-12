<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Auth\User;

use App\Contracts\GraphQL\HasDirectives;
use App\Contracts\GraphQL\HasFields;
use App\Contracts\GraphQL\HasRelations;
use App\GraphQL\Definition\Fields\Auth\User\UserEmailField;
use App\GraphQL\Definition\Fields\Auth\User\UserEmailVerifiedAtField;
use App\GraphQL\Definition\Fields\Auth\User\UserNameField;
use App\GraphQL\Definition\Fields\Auth\User\UserTwoFactorConfirmedAtField;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Relations\BelongsToManyRelation;
use App\GraphQL\Definition\Relations\HasManyRelation;
use App\GraphQL\Definition\Relations\MorphManyRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\List\PlaylistType;
use App\GraphQL\Definition\Types\User\NotificationType;
use App\GraphQL\Definition\Types\Wiki\VideoType;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MeType.
 */
class MeType extends EloquentType implements HasDirectives, HasFields, HasRelations
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
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
            new MorphManyRelation(new NotificationType(), User::RELATION_NOTIFICATIONS),
            new HasManyRelation(new PlaylistType(), User::RELATION_PLAYLISTS),
            new BelongsToManyRelation(new PlaylistType(), 'likedplaylists'),
            new BelongsToManyRelation(new VideoType(), 'likedvideos'),
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
            new IdField(User::ATTRIBUTE_ID),
            new UserNameField(),
            new UserEmailField(),
            new UserEmailVerifiedAtField(),
            new UserTwoFactorConfirmedAtField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }

    /**
     * The directives of the type.
     *
     * @return array<string, array>
     */
    public function directives(): array
    {
        return [
            'model' => [
                'class' => $this->model(),
            ],
        ];
    }

    /**
     * Get the model string representation for the type.
     *
     * @return class-string<Model>
     */
    public function model(): string
    {
        return User::class;
    }
}
