<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Unions;

use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Definition\Types\Wiki\ArtistType;
use App\GraphQL\Definition\Types\Wiki\Song\MembershipType;

class PerformanceArtistUnion extends BaseUnion
{
    /**
     * The description of the union type.
     */
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
