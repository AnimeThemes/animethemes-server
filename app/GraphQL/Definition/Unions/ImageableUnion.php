<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Unions;

use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Definition\Types\Wiki\AnimeType;
use App\GraphQL\Definition\Types\Wiki\ArtistType;
use App\GraphQL\Definition\Types\Wiki\StudioType;

class ImageableUnion extends BaseUnion
{
    /**
     * The name of the union type.
     * By default, it will be the class name.
     */
    public function getName(): string
    {
        return 'Imageable';
    }

    /**
     * The description of the union type.
     */
    public function description(): string
    {
        return 'Represents the types that have images';
    }

    /**
     * The types that this union can resolve to.
     *
     * @return BaseType[]
     */
    public function baseTypes(): array
    {
        return [
            new AnimeType(),
            new ArtistType(),
            new StudioType(),
        ];
    }
}
