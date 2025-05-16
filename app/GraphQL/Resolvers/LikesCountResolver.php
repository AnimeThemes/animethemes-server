<?php

declare(strict_types=1);

namespace App\GraphQL\Resolvers;

use App\Contracts\Models\HasAggregateLikes;
use App\Models\Aggregate\LikeAggregate;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

/**
 * Class LikesCountResolver.
 */
class LikesCountResolver
{
    /**
     * Resolve likes count field.
     *
     * @param  HasAggregateLikes  $likeable
     * @param  array  $args
     * @param  GraphQLContext  $context
     * @param  ResolveInfo  $resolveInfo
     * @return mixed
     */
    public function resolve(HasAggregateLikes $likeable, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): mixed
    {
        /** @var LikeAggregate|null $like */
        /** @phpstan-ignore-next-line */
        $like = $likeable->likeAggregate;

        return (int) $like?->value;
    }
}
