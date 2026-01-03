<?php

declare(strict_types=1);

namespace App\GraphQL\Argument;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use GraphQL\Type\Definition\Type;

class FilterArgument extends Argument
{
    protected bool $required = false;
    protected mixed $defaultValue = null;

    public function __construct(
        protected string $name,
        public Type|string $returnType,
        protected ComparisonOperator $operator,
    ) {
        parent::__construct($name, $returnType);
    }

    public function getComparisonOperator(): ComparisonOperator
    {
        return $this->operator;
    }
}
