<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Unions;

use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Schema\Types\List\PlaylistType;
use App\GraphQL\Schema\Types\Wiki\VideoType;

class LikedUnion extends BaseUnion
{
    /**
     * The name of the union type.
     * By default, it will be the class name.
     */
    public function getName(): string
    {
        return 'Liked';
    }

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
