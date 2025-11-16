<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\User;

use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\User\Like\LikeAnimeThemeEntryField;
use App\GraphQL\Schema\Fields\User\Like\LikePlaylistField;
use App\GraphQL\Schema\Types\Auth\UserType;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Unions\LikedUnion;
use App\GraphQL\Schema\Relations\BelongsToRelation;
use App\GraphQL\Schema\Relations\MorphToRelation;
use App\GraphQL\Schema\Relations\Relation;
use App\Models\User\Like;

class LikeType extends EloquentType
{
    public function description(): string
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
    public function fieldClasses(): array
    {
        return [
            new LikeAnimeThemeEntryField(),
            new LikePlaylistField(),
        ];
    }
}
