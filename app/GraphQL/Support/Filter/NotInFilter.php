<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Filter;

use App\GraphQL\Support\Argument\Argument;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;

class NotInFilter extends Filter
{
    /**
     * Get the argument to apply the filter.
     */
    public function argument(): Argument
    {
        return new Argument($this->field->getName().'_not_in', Type::listOf(Type::nonNull($this->field->baseType())))
            ->withDefaultValue($this->defaultValue);
    }

    /**
     * Apply the filter to the builder.
     */
    public function apply(Builder $builder, mixed $value): Builder
    {
        return $builder->whereNotIn(
            $this->field->getColumn(),
            $value,
        );
    }
}
