<?php

declare(strict_types=1);

namespace App\GraphQL\Filter;

use App\GraphQL\Argument\Argument;
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

    public function getBaseType(): Type
    {
        return Type::listOf(Type::nonNull(GraphQL::type(new WhereConditionsInput($this->type)->getName())));
    }

    /**
     * @return Argument[]
     */
    public function getArguments(): array
    {
        return [
            new Argument('where', $this->getBaseType())
                ->withDefaultValue($this->defaultValue),
        ];
    }

    /**
     * Convert filter values if needed. By default, no conversion is needed.
     */
    protected function convertFilterValues(array $filterValues): array
    {
        return $filterValues;
    }
}
