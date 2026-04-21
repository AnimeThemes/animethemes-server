<?php

declare(strict_types=1);

namespace App\GraphQL\WhereConditions;

use App\Contracts\GraphQL\EnumFilterableColumns;
use GraphQL\Error\Error;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\WhereConditions\WhereConditionsHandler as BaseWhereConditionsHandler;

use function Safe\preg_match;

class WhereConditionsHandler extends BaseWhereConditionsHandler
{
    /**
     * @template TModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  QueryBuilder|EloquentBuilder<TModel>  $builder
     * @param  array<string, mixed>  $whereConditions
     * @param  TModel|null  $model
     */
    public function __invoke(
        object $builder,
        array $whereConditions,
        ?Model $model = null,
        string $boolean = 'and',
    ): void {
        if ($builder instanceof EloquentBuilder) {
            $model = $builder->getModel();
        }

        $column = Arr::get($whereConditions, 'column');

        if ($column instanceof EnumFilterableColumns) {
            $filter = $column->getFilter();

            $whereConditions['value'] = $filter->getFilterValues(Arr::wrap($whereConditions['value']));

            // Return the column key value to its normal state.
            $whereConditions['column'] = $filter->getColumn();
        }

        if ($andConnectedConditions = $whereConditions['AND'] ?? null) {
            $builder->whereNested(
                function (QueryBuilder|EloquentBuilder $builder) use ($andConnectedConditions, $model): void {
                    foreach ($andConnectedConditions as $condition) {
                        $this->__invoke($builder, $condition, $model);
                    }
                },
                $boolean,
            );
        }

        if ($orConnectedConditions = $whereConditions['OR'] ?? null) {
            $builder->whereNested(
                function (QueryBuilder|EloquentBuilder $builder) use ($orConnectedConditions, $model): void {
                    foreach ($orConnectedConditions as $condition) {
                        $this->__invoke($builder, $condition, $model, 'or');
                    }
                },
                $boolean,
            );
        }

        if (($hasRelationConditions = $whereConditions['HAS'] ?? null) && $model) {
            $nestedBuilder = $this->handleHasCondition(
                $model,
                $hasRelationConditions['relation'],
                $hasRelationConditions['operator'],
                $hasRelationConditions['amount'],
                $hasRelationConditions['condition'] ?? null,
            );
            $builder->addNestedWhereQuery($nestedBuilder, $boolean);
        }

        if ($column = $whereConditions['column'] ?? null) {
            $this->assertValidColumnReference($column);
            $this->operator->applyConditions($builder, $whereConditions, $boolean);
        }
    }

    /** @param  array<string, mixed>|null  $condition */
    public function handleHasCondition(
        Model $model,
        string $relation,
        string $operator,
        int $amount,
        ?array $condition = null,
    ): QueryBuilder {
        return $model
            ->newQuery()
            ->whereHas(
                $relation,
                $condition
                    ? function (EloquentBuilder $builder) use ($condition): void {
                        $this->__invoke(
                            $builder,
                            $this->prefixConditionWithTableName(
                                $condition,
                                $builder->getModel(),
                            ),
                            $builder->getModel(),
                        );
                    }
                : null,
                $operator,
                $amount,
            )
            ->getQuery();
    }

    /** Ensure the column name is well formed to prevent SQL injection. */
    protected function assertValidColumnReference(string $column): void
    {
        // A valid column reference:
        // - must not start with a digit, dot or hyphen
        // - must contain only alphanumerics, digits, underscores, dots, hyphens or JSON references
        $match = preg_match('/^(?![0-9.-])([A-Za-z0-9_.-]|->)*$/', $column);
        if ($match === 0) {
            throw new Error(self::invalidColumnName($column));
        }
    }

    public static function invalidColumnName(string $column): string
    {
        return "Column names may contain only alphanumerics or underscores, and may not begin with a digit, got: {$column}";
    }

    /**
     * If the condition references a column, prefix it with the table name.
     *
     * This is important for queries that can otherwise be ambiguous.
     * For example, ambiguity happens when multiple tables include a column named "id".
     *
     * @param  array<string, mixed>  $condition
     * @return array<string, mixed>
     */
    protected function prefixConditionWithTableName(array $condition, Model $model): array
    {
        if (isset($condition['column'])) {
            $condition['column'] = "{$model->getTable()}.{$condition['column']}";
        }

        return $condition;
    }
}
