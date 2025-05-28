<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Unions;

use App\GraphQL\Definition\Types\List\PlaylistType;
use App\GraphQL\Definition\Types\Wiki\VideoType;
use GraphQL\Type\Definition\Type;

/**
 * Class LikedUnion.
 */
class LikedUnion extends BaseUnion
{
    /**
     * The description of the union type.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Represents the resource type performing';
    }

    /**
     * The types that this union can resolve to.
     *
     * @return array<Type>
     */
    public function types(): array
    {
        return [
            new PlaylistType(),
            new VideoType(),
        ];
    }
}
