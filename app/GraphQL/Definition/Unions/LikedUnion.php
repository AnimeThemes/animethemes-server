<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Unions;

use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Definition\Types\List\PlaylistType;
use App\GraphQL\Definition\Types\Wiki\VideoType;

class LikedUnion extends BaseUnion
{
    /**
     * The name of the union type.
     */
    public function name(): string
    {
        return 'Liked';
    }

    /**
     * The description of the union type.
     */
    public function description(): string
    {
        return 'Represents the resources that can be liked';
    }

    /**
     * The types that this union can resolve to.
     *
     * @return BaseType[]
     */
    public function baseTypes(): array
    {
        return [
            new PlaylistType(),
            new VideoType(),
        ];
    }
}
