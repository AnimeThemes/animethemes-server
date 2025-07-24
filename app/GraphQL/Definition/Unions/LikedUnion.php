<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Unions;

use App\GraphQL\Definition\Types\List\PlaylistType;
use App\GraphQL\Definition\Types\Wiki\VideoType;
use GraphQL\Type\Definition\Type;

class LikedUnion extends BaseUnion
{
    /**
     * The name of the union type.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Liked';
    }

    /**
     * The description of the union type.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Represents the resources that can be liked';
    }

    /**
     * The types that this union can resolve to.
     *
     * @return Type[]
     */
    public function types(): array
    {
        return [
            new PlaylistType(),
            new VideoType(),
        ];
    }
}
