<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\User;

use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Relations\BelongsToRelation;
use App\GraphQL\Schema\Fields\Relations\MorphToRelation;
use App\GraphQL\Schema\Fields\User\Like\LikeAnimeThemeEntryField;
use App\GraphQL\Schema\Fields\User\Like\LikePlaylistField;
use App\GraphQL\Schema\Types\Auth\UserType;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Unions\LikeableUnion;
use App\Models\User\Like;

class LikeType extends EloquentType
{
    public function description(): string
    {
        return 'Represents a like of a user.';
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new LikeAnimeThemeEntryField(),
            new LikePlaylistField(),

            new BelongsToRelation(new UserType(), Like::RELATION_USER),
            new MorphToRelation(new LikeableUnion(), Like::RELATION_LIKEABLE),
        ];
    }
}
