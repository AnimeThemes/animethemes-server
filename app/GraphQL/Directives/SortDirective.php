<?php

declare(strict_types=1);

namespace App\GraphQL\Directives;

use App\Contracts\GraphQL\EnumSort;
use App\Enums\Http\Api\Field\AggregateFunction;
use App\GraphQL\Sort\FieldSortCriteria;
use GraphQL\Error\Error;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Laravel\Scout\Builder as ScoutBuilder;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Scout\ScoutBuilderDirective;
use Nuwave\Lighthouse\Support\Contracts\ArgBuilderDirective;
use Nuwave\Lighthouse\Support\Contracts\ArgDirective;
use UnitEnum;

class SortDirective extends BaseDirective implements ArgBuilderDirective, ArgDirective, ScoutBuilderDirective
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
directive @sort on ARGUMENT_DEFINITION
GRAPHQL;
    }

    /**
     * Add additional constraints to the builder based on the given argument value.
     *
     * @param  QueryBuilder|EloquentBuilder|Relation  $builder  the builder used to resolve the field
     * @param  array<int, UnitEnum&EnumSort>  $value  the client given value of the argument
     * @return QueryBuilder|EloquentBuilder|Relation the modified builder
     */
    public function handleBuilder(QueryBuilder|EloquentBuilder|Relation $builder, mixed $value): QueryBuilder|EloquentBuilder|Relation
    {
        foreach ($value as $sort) {
            $criteria = $sort->getSortCriteria();

            if ($criteria->aggregateRelation !== null) {
                match ($criteria->function) {
                    AggregateFunction::EXISTS,
                    AggregateFunction::COUNT => $builder->{"with{$criteria->function->value}"}($criteria->aggregateRelation),
                    default => $builder->{"with{$criteria->function->value}"}($criteria->aggregateRelation, $criteria->getColumn()),
                };
            }

            $criteria->sort($builder);
        }

        return $builder;
    }

    /**
     * Modify the scout builder with a client given value.
     *
     * @param  array<int, UnitEnum&EnumSort>  $value  the client given value of the argument
     */
    public function handleScoutBuilder(ScoutBuilder $builder, mixed $value): ScoutBuilder
    {
        foreach ($value as $sort) {
            $criteria = $sort->getSortCriteria();

            match (true) {
                $criteria instanceof FieldSortCriteria => $builder->orderBy($criteria->getColumn(), $criteria->getDirection()->value),
                default => throw new Error('Sorting by this value is not supported when using the \'search\' parameter.'),
            };
        }

        return $builder;
    }
}
