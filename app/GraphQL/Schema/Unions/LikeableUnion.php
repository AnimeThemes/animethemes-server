<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Unions;

use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Schema\Types\List\PlaylistType;
use App\GraphQL\Schema\Types\Wiki\Anime\Theme\AnimeThemeEntryType;

class LikeableUnion extends BaseUnion
{
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
            new AnimeThemeEntryType(),
            new PlaylistType(),
        ];
    }
}
