<?php

declare(strict_types=1);

namespace App\GraphQL\Pagination;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ObjectType;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Pagination\ConnectionField;
use Nuwave\Lighthouse\Pagination\Cursor;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ConnectionType extends ConnectionField
{
    public function nodesResolver(Paginator $paginator, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Collection
    {
        return new Collection(array_values($paginator->items()));
    }

    /**
     * @param  \Illuminate\Contracts\Pagination\Paginator<*, *>  $paginator
     * @param  array<string, mixed>  $args
     * @return Collection<int, array<string, mixed>>
     */
    public function edgeResolver(Paginator $paginator, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Collection
    {
        // We know those types because we manipulated them during PaginationManipulator
        $nonNullList = $resolveInfo->returnType;
        assert($nonNullList instanceof NonNull);

        $objectLikeType = $nonNullList->getInnermostType();
        assert($objectLikeType instanceof ObjectType || $objectLikeType instanceof InterfaceType);

        $returnTypeFields = $objectLikeType->getFields();

        /** @var int|null $firstItem Laravel type-hints are inaccurate here */
        $firstItem = $paginator->firstItem();

        $values = new Collection(array_values($paginator->items()));

        return $values->map(function (Model $item, int $index) use ($returnTypeFields, $firstItem): array {
            $edges = [];
            foreach ($returnTypeFields as $field) {
                $relation = current($item->getRelations());

                if ($field->name === 'cursor') {
                    $edges['cursor'] = Cursor::encode((int) $firstItem + $index);
                } elseif ($field->name === 'node') {
                    $edges['node'] = $item;
                } elseif ($relation instanceof Model) {
                    // TODO: Currently we assume the field has the column name as camelCase,
                    // so we apply the reverse engine here, we should check if it is possible
                    // to get the exact column name without losing performance.
                    $edges[$field->name] = $relation->getAttribute(Str::snake($field->name));
                }
            }

            return $edges;
        });
    }
}
