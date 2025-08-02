<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\User;

use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\User\Like\LikePlaylistField;
use App\GraphQL\Definition\Fields\User\Like\LikeVideoField;
use App\GraphQL\Definition\Types\Auth\UserType;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Unions\LikedUnion;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\MorphToRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\User\Like;

class LikeType extends EloquentType
{
    /**
     * The description of the type.
     */
    public function getDescription(): string
    {
        return 'Represents a like of a user.';
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new UserType(), Like::RELATION_USER),
            new MorphToRelation(new LikedUnion(), Like::RELATION_LIKEABLE)
                ->renameTo('liked'),
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
            new LikePlaylistField(),
            new LikeVideoField(),
        ];
    }
}
