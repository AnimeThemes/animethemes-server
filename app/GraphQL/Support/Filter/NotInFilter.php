<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Filter;

use App\GraphQL\Support\Argument\Argument;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;

class NotInFilter extends Filter
{
    public function argument(): Argument
    {
        return new Argument($this->field->getName().'_not_in', Type::listOf(Type::nonNull($this->field->baseType())))
            ->withDefaultValue($this->defaultValue);
    }

    public function filter(Builder $builder, mixed $value): Builder
    {
        return $builder->whereNotIn(
            $builder->qualifyColumn($this->field->getColumn()),
            $value,
        );
    }
}
