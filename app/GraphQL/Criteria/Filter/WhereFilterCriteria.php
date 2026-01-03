<?php

declare(strict_types=1);

namespace App\GraphQL\Criteria\Filter;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\GraphQL\Schema\Fields\Field;
use Illuminate\Database\Eloquent\Builder;

class WhereFilterCriteria extends FilterCriteria
{
    public function __construct(
        protected Field $field,
        protected ComparisonOperator $operator,
        protected mixed $value,
    ) {}

    /**
     * Apply the filtering to the current Eloquent builder.
     */
    public function filter(Builder $builder): Builder
    {
        return $builder->where(
            $builder->qualifyColumn($this->field->getColumn()),
            $this->operator->value,
            $this->value
        );
    }
}
