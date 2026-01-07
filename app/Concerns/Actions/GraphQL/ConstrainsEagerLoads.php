<?php

declare(strict_types=1);

namespace App\Concerns\Actions\GraphQL;

use App\GraphQL\ResolveInfo as CustomResolveInfo;
use App\GraphQL\Schema\Relations\Relation;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Unions\BaseUnion;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Support\Arr;

trait ConstrainsEagerLoads
{
    use FiltersModels;
    use SortsModels;

    /**
     * Apply eager loads with filters and sorting.
     */
    protected function constrainEagerLoads(Builder $query, ResolveInfo $resolveInfo, BaseType $type, string $fieldName = 'data'): void
    {
        $resolveInfo = new CustomResolveInfo($resolveInfo);

        $selection = Arr::get($resolveInfo->getFieldSelectionWithAliases(100), "{$fieldName}.{$fieldName}.selectionSet")
            ?? $resolveInfo->getFieldSelectionWithAliases(100);

        $this->processEagerLoadForType($query, $selection, $type);
    }

    /**
     * Process recursively the relations available for the given type.
     */
    private function processEagerLoadForType(Builder $builder, array $selection, BaseType|BaseUnion $type): void
    {
        $eagerLoadRelations = [];

        /** @var array<int, Relation> $relations */
        $relations = collect($type->relations())
            ->filter(fn (Relation $relation) => Arr::has($selection, $relation->getName()))
            ->all();

        foreach ($relations as $relation) {
            $name = $relation->getName();

            $relationSelection = Arr::get($selection, "{$name}.{$name}");

            $relationType = $relation->getBaseType();

            $eagerLoadRelations[$relation->getRelationName()] = function (EloquentRelation $eloquentRelation) use ($relationSelection, $relationType, $relation): void {
                match (true) {
                    $eloquentRelation instanceof MorphTo => $this->processMorphToRelation($relationSelection, $relationType, $eloquentRelation),
                    $relationType instanceof BaseUnion => $this->processUnion($relationSelection, $relationType, $eloquentRelation),
                    default => $this->processGenericRelation($relationSelection, $relationType, $eloquentRelation, $relation),
                };
            };
        }

        $builder->with($eagerLoadRelations);
    }

    /**
     * MorphTo relationships have to be handled differently since they can have multiple types.
     */
    private function processMorphToRelation(array $selection, BaseUnion $union, MorphTo $relation): void
    {
        $unions = Arr::get($selection, 'unions');

        /** @var array<int, EloquentType> $types */
        $types = collect($union->baseTypes())
            ->filter(fn (BaseType $type): bool => $type instanceof EloquentType)
            ->filter(fn (EloquentType $type) => Arr::has($unions, $type->getName()))
            ->all();

        $morphConstrains = [];
        foreach ($types as $type) {
            $typeSelection = Arr::get($unions, "{$type->getName()}.selectionSet", []);

            $morphConstrains[$type->model()] = function (Builder $query) use ($typeSelection, $type): void {
                $this->processEagerLoadForType($query, $typeSelection, $type);
            };
        }

        $relation->constrain($morphConstrains);
    }

    /**
     * Process a union relation by applying the eager loads for each type in the union.
     */
    private function processUnion(array $selection, BaseUnion $union, EloquentRelation $relation): void
    {
        $unions = Arr::get($selection, 'selectionSet.data.data.unions', []);

        $types = collect($union->baseTypes())
            ->filter(fn (BaseType $type) => Arr::has($unions, $type->getName()))
            ->all();

        foreach ($types as $type) {
            $typeSelection = Arr::get($unions, "{$type->getName()}.selectionSet", []);

            $this->processEagerLoadForType($relation->getQuery(), $typeSelection, $type);
        }
    }

    /**
     * Process a generic relation by applying filters, sorting and eager loads.
     */
    private function processGenericRelation(array $selection, BaseType $type, EloquentRelation $relation, Relation $graphqlRelation): void
    {
        $builder = $relation->getQuery();

        $args = Arr::get($selection, 'args');

        $this->filter($builder, $args, $type);

        $this->sort($builder, $args, $type, $relation, $graphqlRelation);

        $fields = Arr::get($selection, 'selectionSet.data.data.selectionSet')
            ?? Arr::get($selection, 'selectionSet.nodes.nodes.selectionSet')
            ?? Arr::get($selection, 'selectionSet', []);

        $this->processEagerLoadForType($builder, $fields, $type);
    }
}
