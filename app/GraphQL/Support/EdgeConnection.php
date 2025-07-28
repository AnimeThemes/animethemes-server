<?php

declare(strict_types=1);

namespace App\GraphQL\Support;

use App\GraphQL\Resolvers\NodesResolver;
use Illuminate\Support\Str;
use Nuwave\Lighthouse\Pagination\ConnectionField;
use Stringable;

/**
 * Overlap the behavior edge connection to accept a custom 'nodes' field.
 */
final readonly class EdgeConnection implements Stringable
{
    public function __construct(
        protected EdgeType $edgeType,
    ) {}

    /**
     * Get the edge connection as a string representation.
     */
    public function __toString(): string
    {
        return sprintf(
            'type %1$sConnection {
                """Pagination information about the list of edges."""
                pageInfo: PageInfo! @field(resolver: "%2$s@pageInfoResolver")
                """A list of %3$s edges."""
                edges: [%1$s!]! @field(resolver: "%2$s@edgeResolver")
                """A list of %3$s resources. Use this if you don\'t care about pivot fields. """
                nodes: [%3$s]! @field(resolver: "%4$s")
            }',
            $this->edgeType->getName(),
            addslashes(ConnectionField::class),
            Str::remove('Type', class_basename($this->edgeType->getNodeType())),
            addslashes(NodesResolver::class),
        );
    }
}
