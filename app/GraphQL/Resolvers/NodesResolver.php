<?php

declare(strict_types=1);

namespace App\GraphQL\Resolvers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class NodesResolver
{
    /**
     * Resolve the nodes field of the edge connection.
     *
     * @param  array<string, mixed>  $args
     */
    public function __invoke(LengthAwarePaginator $paginator, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): mixed
    {
        return new Collection(array_values($paginator->items()));
    }
}
