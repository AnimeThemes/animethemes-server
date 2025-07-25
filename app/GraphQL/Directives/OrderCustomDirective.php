<?php

declare(strict_types=1);

namespace App\GraphQL\Directives;

use App\Enums\GraphQL\OrderDirection;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Arr;
use InvalidArgumentException;
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
        directive @orderCustom(column: String!) on ARGUMENT_DEFINITION
        GRAPHQL;
    }

    /**
     * @param  int  $directionValue
     */
    public function handleBuilder(QueryBuilder|EloquentBuilder|Relation $builder, $directionValue): QueryBuilder|EloquentBuilder|Relation
    {
        $definition = $this->definitionNode->toArray();

        $directives = Arr::get($definition, 'directives');

        foreach ($directives as $directive) {
            if (Arr::get($directive, 'name.value') === $this->name()) {
                foreach (Arr::get($directive, 'arguments') as $argument) {
                    if (Arr::get($argument, 'name.value') === 'column') {
                        $column = Arr::get($argument, 'value.value');
                        break 2;
                    }
                }
            }
        }

        if (!isset($column)) {
            throw new InvalidArgumentException('The "column" argument is required for the @orderCustom directive.');
        }

        $direction = OrderDirection::from($directionValue);

        return $builder->orderBy($column, $direction->name);
    }
}
