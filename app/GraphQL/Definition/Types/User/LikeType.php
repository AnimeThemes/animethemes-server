<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\User;


use App\GraphQL\Definition\Relations\BelongsToRelation;
use App\GraphQL\Definition\Relations\MorphToRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\Auth\UserType;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Definition\Unions\LikedUnion;
use App\Models\User\Like;

/**
 * Class LikeType.
 */
class LikeType extends BaseType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents a like of a user.";
    }

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new UserType(), Like::RELATION_USER),
            new MorphToRelation(new LikedUnion(), Like::RELATION_LIKEABLE, 'liked'),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return array
     */
    public function fields(): array
    {
        return [];
    }
}
