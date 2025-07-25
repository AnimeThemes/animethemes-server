<?php

declare(strict_types=1);

namespace App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Eloquent\Model;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ImplodeArrayResolver
{
    /**
     * Resolve the relation count field.
     *
     * @param  array<string, mixed>  $args
     */
    public function __invoke(Model $model, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): mixed
    {
        return implode('', $model->getAttribute($resolveInfo->fieldName));
    }
}
