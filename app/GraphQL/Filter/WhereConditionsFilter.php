<?php

declare(strict_types=1);

namespace App\GraphQL\Filter;

use App\GraphQL\Argument\Argument;
use App\GraphQL\Criteria\Filter\WhereConditionsFilterCriteria;
use App\GraphQL\Schema\Inputs\WhereConditionsInput;
use App\GraphQL\Schema\Types\EloquentType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class WhereConditionsFilter extends Filter
{
    public function __construct(
        protected EloquentType $type,
        protected mixed $defaultValue = null,
    ) {}

    public function argument(): Argument
    {
        return new Argument('where', Type::listOf(Type::nonNull(GraphQL::type(new WhereConditionsInput($this->type)->getName()))))
            ->withDefaultValue($this->defaultValue);
    }

    public function criteria(mixed $value): WhereConditionsFilterCriteria
    {
        return new WhereConditionsFilterCriteria($value, $this->type);
    }
}
