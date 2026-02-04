<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Unions;

use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Schema\Types\Wiki\AnimeType;
use App\GraphQL\Schema\Types\Wiki\ArtistType;
use App\GraphQL\Schema\Types\Wiki\SongType;
use App\GraphQL\Schema\Types\Wiki\StudioType;

class ResourceableUnion extends BaseUnion
{
    public function description(): string
    {
        return 'Represents the types that have resources';
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
            new SongType(),
            new StudioType(),
        ];
    }
}
