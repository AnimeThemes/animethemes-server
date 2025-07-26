<?php

declare(strict_types=1);

namespace App\GraphQL\Directives;

use App\Enums\GraphQL\SortType;
use App\Exceptions\GraphQL\ClientValidationException;
use App\GraphQL\Definition\Sort\SortableColumns;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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

        """
        This input is used to coordinate the code to get the client argument and matches with the column sortable.
        """
        input SortByColumn {
            column: String!
            value: String!
            sortType: Int! = 0
            relation: String
        }
        GRAPHQL;
    }

    /**
     * @param  array<string, mixed>  $sortByColumns
     *
     * @throws ClientValidationException
     * @throws InvalidArgumentException
     */
    public function handleBuilder(QueryBuilder|EloquentBuilder|Relation $builder, $sortByColumns): QueryBuilder|EloquentBuilder|Relation
    {
        $sortableColumns = json_decode($this->directiveArgValue('columns'), true);

        foreach ($sortByColumns as $column) {
            $direction = SortableColumns::resolveSortDirection($column)->value;

            $sortByColumnValue = Str::remove('_DESC', $column);

            $sortByColumnInput = Arr::first($sortableColumns, fn ($sortByColumn) => $sortByColumn['value'] === $sortByColumnValue);

            $column = Arr::get($sortByColumnInput, 'column');
            $sortType = SortType::from(Arr::get($sortByColumnInput, 'sortType'));

            if ($sortType === SortType::ROOT) {
                $builder->orderBy($column, $direction);
            }

            if ($sortType === SortType::AGGREGATE) {
                $relation = Arr::get($sortByColumnInput, 'relation');
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
