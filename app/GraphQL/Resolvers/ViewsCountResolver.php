<?php

declare(strict_types=1);

namespace App\GraphQL\Resolvers;

use App\Contracts\Models\HasAggregateViews;
use App\Models\Aggregate\ViewAggregate;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

/**
 * Class ViewsCountResolver.
 */
class ViewsCountResolver
{
    /**
     * Resolve views count field.
     *
     * @param  HasAggregateViews  $viewable
     * @param  array  $args
     * @param  GraphQLContext  $context
     * @param  ResolveInfo  $resolveInfo
     * @return mixed
     */
    public function resolve(HasAggregateViews $viewable, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): mixed
    {
        /** @var ViewAggregate|null $view */
        /** @phpstan-ignore-next-line */
        $view = $viewable->viewAggregate;

        return (int) $view?->value;
    }
}
