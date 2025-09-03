<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Unions;

use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Schema\Types\Wiki\AnimeType;
use App\GraphQL\Schema\Types\Wiki\ArtistType;
use App\GraphQL\Schema\Types\Wiki\StudioType;

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
