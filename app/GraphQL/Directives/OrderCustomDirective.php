<?php

declare(strict_types=1);

namespace App\GraphQL\Directives;

use App\Enums\GraphQL\OrderDirection;
use App\Enums\GraphQL\OrderType;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use League\Csv\InvalidArgument;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Support\Contracts\ArgBuilderDirective;

class OrderCustomDirective extends BaseDirective implements ArgBuilderDirective
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
        """
        Sort a result by the given argument.
        """
        directive @orderCustom(columns: [OrderByColumn!]) on ARGUMENT_DEFINITION

        input OrderByColumn {
            column: String!
            orderType: Int! = 0
            relation: String
        }
        GRAPHQL;
    }

    /**
     * @param  array<string, mixed>  $directionValue
     *
     * @throws InvalidArgumentException
     */
    public function handleBuilder(QueryBuilder|EloquentBuilder|Relation $builder, $orderByColumns): QueryBuilder|EloquentBuilder|Relation
    {
        $orderableColumns = json_decode($this->directiveArgValue('columns'), true);

        foreach ($orderByColumns as $orderByColumn) {
            $column = Arr::pull($orderByColumn, 'column');
            $direction = OrderDirection::from(intval(Arr::pull($orderByColumn, 'direction')));

            $object = collect($orderableColumns)
                ->first(fn (array $value) => $value['column'] === $column);

            if ($object === null) {
                throw new InvalidArgument("The column '{$column}' is not available for ordering.");
            }

            $orderType = OrderType::from(Arr::get($object, 'orderType'));

            if ($orderType === OrderType::ROOT) {
                $builder->orderBy($column, $direction->name);
            }

            if ($orderType === OrderType::AGGREGATE) {
                $relation = Arr::get($object, 'relation');
                if ($relation === null) {
                    throw new InvalidArgumentException('The "relation" argument is required for the @orderCustom directive with aggregate order type.');
                }

                $builder->withAggregate([
                    "$relation as {$relation}_value" => function ($query) use ($direction) {
                        $query->orderBy('value', $direction->name);
                    }
                ], 'value');

                $builder->orderBy("{$relation}_value", $direction->name);
            }
        }

        return $builder;
    }
}
