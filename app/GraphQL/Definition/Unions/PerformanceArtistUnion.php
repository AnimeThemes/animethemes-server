<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Unions;

use App\GraphQL\Definition\Types\Wiki\ArtistType;
use App\GraphQL\Definition\Types\Wiki\Song\MembershipType;
use GraphQL\Type\Definition\Type;

/**
 * Class PerformanceArtistUnion.
 */
class PerformanceArtistUnion extends BaseUnion
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
     * @return Type[]
     */
    public function types(): array
    {
        return [
            new ArtistType(),
            new MembershipType(),
        ];
    }
}
