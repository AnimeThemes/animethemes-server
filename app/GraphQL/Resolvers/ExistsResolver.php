<?php

declare(strict_types=1);

namespace App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

/**
 * Class ExistsResolver.
 */
class ExistsResolver
{
    /**
     * Resolve the relation exists field.
     *
     * @param  Model  $model
     * @param  array  $args
     * @param  GraphQLContext  $context
     * @param  ResolveInfo  $resolveInfo
     * @return mixed
     */
    public function resolve(Model $model, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): mixed
    {
        // Field expected to be {relation}_exists
        $relation = Str::remove('_exists', $resolveInfo->fieldName);

        return $model->{$relation}->isNotEmpty();
    }
}
