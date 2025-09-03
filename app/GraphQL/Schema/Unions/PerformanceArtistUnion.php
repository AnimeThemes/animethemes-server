<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Unions;

use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Schema\Types\Wiki\ArtistType;
use App\GraphQL\Schema\Types\Wiki\Song\MembershipType;

class PerformanceArtistUnion extends BaseUnion
{
    public function description(): string
    {
        return 'Represents the resource type performing';
    }

    /**
     * The types that this union can resolve to.
     *
     * @return BaseType[]
     */
    public function baseTypes(): array
    {
        return [
            new ArtistType(),
            new MembershipType(),
        ];
    }
}
