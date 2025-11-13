<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Filter;

use App\GraphQL\Criteria\Filter\WhereInFilterCriteria;
use App\GraphQL\Support\Argument\Argument;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;

class InFilter extends Filter
{
    public function argument(): Argument
    {
        return new Argument($this->field->getName().'_in', Type::listOf(Type::nonNull($this->field->baseType())))
            ->withDefaultValue($this->defaultValue);
    }

    public function filter(Builder $builder, mixed $value): Builder
    {
        return $builder->whereIn(
            $builder->qualifyColumn($this->field->getColumn()),
            $value,
        );
    }

    public function criteria(mixed $value): WhereInFilterCriteria
    {
        return new WhereInFilterCriteria(
            $this,
            $value
        );
    }
}
