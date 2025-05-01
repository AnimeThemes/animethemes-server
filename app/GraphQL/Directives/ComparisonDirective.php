<?php

declare(strict_types=1);

namespace App\GraphQL\Directives;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Support\Contracts\ArgBuilderDirective;

/**
 * Class ComparisonDirective.
 */
class ComparisonDirective extends BaseDirective implements ArgBuilderDirective
{
    /**
     * Define the directive.
     *
     * @return string
     */
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
        """
        Use the client given value to add a lesser than conditional to a database query.
        """
        directive @comparison(
        """
        Specify the database column to compare.
        Only required if database column has a different name than the attribute in your schema.
        """
        key: String
        """
        Specify the comparison operator.
        """
        operator: String
        """
        Specify if the value is a date.
        """
        date: Boolean = false
        ) repeatable on ARGUMENT_DEFINITION | INPUT_FIELD_DEFINITION
        GRAPHQL;
    }

    /**
     * Handle the builder.
     *
     * @param  QueryBuilder|EloquentBuilder|Relation  $builder
     * @param  mixed  $value
     * @return QueryBuilder|EloquentBuilder|Relation
     */
    public function handleBuilder(QueryBuilder|EloquentBuilder|Relation $builder, $value): QueryBuilder|EloquentBuilder|Relation
    {
        $method = $this->directiveArgValue('date') ? 'whereDate' : 'where';

        return $builder->{$method}(
            $this->directiveArgValue('key', $this->nodeName()),
            ComparisonOperator::{$this->directiveArgValue('operator')}->value,
            $value,
        );
    }
}
