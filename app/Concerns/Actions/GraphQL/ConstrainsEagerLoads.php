<?php

declare(strict_types=1);

namespace App\Concerns\Actions\GraphQL;

use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Definition\Unions\BaseUnion;
use App\GraphQL\Support\Relations\Relation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation as RelationsRelation;
use Illuminate\Support\Arr;

trait ConstrainsEagerLoads
{
    use FiltersModels;
    use SortsModels;

    /**
     * Apply eager loads with filters and sorting.
     */
    protected function constrainEagerLoads(Builder $query, ResolveInfo|array $resolveInfo, BaseType $type): void
    {
        $fields = $resolveInfo instanceof ResolveInfo
            ? Arr::get($resolveInfo->getFieldSelectionWithAliases(100), 'data.data.selectionSet')
            : $resolveInfo;

        $this->processEagerLoadForType($query, $fields, $type);
    }

    /**
     * Process recursively the relations available for the given type.
     */
    private function processEagerLoadForType(Builder $builder, array $fields, BaseType|BaseUnion $type): void
    {
        $eagerLoadRelations = [];

        if ($type instanceof BaseUnion) {
            return;
        }

        /** @var array<int, Relation> $relations */
        $relations = collect($type->relations())
            ->filter(fn (Relation $relation) => Arr::has($fields, $relation->getName()))
            ->toArray();

        foreach ($relations as $relation) {
            $name = $relation->getName();
            $path = $relation->getRelationName();

            $relationSelection = Arr::get($fields, $name.'.'.$name);

            $relationArgs = Arr::get($relationSelection, 'args');

            $relationType = $relation->getBaseType();

            $eagerLoadRelations[$path] = function (RelationsRelation $relationLaravel) use ($relationSelection, $relationArgs, $relationType) {
                $relationQuery = $relationLaravel->getQuery();

                $this->filter($relationQuery, $relationArgs, $relationType);

                $this->sort($relationQuery, $relationArgs, $relationType);

                $child = Arr::get($relationSelection, 'selectionSet.data.data.selectionSet')
                    ?? Arr::get($relationSelection, 'selectionSet', []);

                $this->processEagerLoadForType($relationQuery, $child, $relationType);
            };
        }

        $builder->with($eagerLoadRelations);
    }
}
