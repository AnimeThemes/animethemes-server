<?php

declare(strict_types=1);

namespace App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class PivotResolver
{
    /**
     * Resolve the pivot field.
     *
     * @param  array|Model  $root
     * @param  array<string, mixed>  $args
     */
    public function __invoke(array|Model $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): mixed
    {
        if ($root instanceof Model) {
            return $root->getAttribute(Str::snake($resolveInfo->fieldName));
        }

        /** @var Model $model */
        $model = Arr::get($root, 'node');

        $pivot = current($model->getRelations());

        return $pivot->{Str::snake($resolveInfo->fieldName)};
    }
}
