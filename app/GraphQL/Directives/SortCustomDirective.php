<?php

declare(strict_types=1);

namespace App\GraphQL\Directives;

use App\Enums\GraphQL\SortDirection;
use App\Enums\GraphQL\SortType;
use App\Exceptions\GraphQL\ClientValidationException;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Support\Contracts\ArgBuilderDirective;

class SortCustomDirective extends BaseDirective implements ArgBuilderDirective
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
        """
        Sort a result by the given argument.
        """
        directive @sortCustom(columns: [SortByColumn!]) on ARGUMENT_DEFINITION

        input SortByColumn {
            column: String!
            sortType: Int! = 0
            relation: String
        }
        GRAPHQL;
    }

    /**
     * @param  array<string, mixed>  $sortByColumns
     *
     * @throws InvalidArgumentException
     */
    public function handleBuilder(QueryBuilder|EloquentBuilder|Relation $builder, $sortByColumns): QueryBuilder|EloquentBuilder|Relation
    {
        $sortableColumns = json_decode($this->directiveArgValue('columns'), true);

        foreach ($sortByColumns as $sortByColumn) {
            $column = Arr::pull($sortByColumn, 'column');
            $direction = Arr::pull($sortByColumn, 'direction');

            $object = collect($sortableColumns)
                ->first(fn (array $value) => $value['column'] === $column);

            if ($object === null) {
                throw new ClientValidationException("The column '{$column}' is not available for ordering.");
            }

            $sortType = SortType::from(Arr::get($object, 'sortType'));

            if ($sortType === SortType::ROOT) {
                $builder->orderBy($column, $direction);
            }

            if ($sortType === SortType::AGGREGATE) {
                $relation = Arr::get($object, 'relation');
                if ($relation === null) {
                    throw new InvalidArgumentException("The 'relation' argument is required for the @{$this->name()} directive with aggregate sort type.");
                }

                $builder->withAggregate([
                    "$relation as {$relation}_value" => function ($query) use ($direction) {
                        $query->orderBy('value', $direction);
                    },
                ], 'value');

                $builder->orderBy("{$relation}_value", $direction);
            }
        }

        return $builder;
    }
}
