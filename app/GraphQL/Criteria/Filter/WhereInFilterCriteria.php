<?php

declare(strict_types=1);

namespace App\GraphQL\Criteria\Filter;

use App\GraphQL\Support\Filter\Filter;
use Illuminate\Database\Eloquent\Builder;

class WhereInFilterCriteria extends FilterCriteria
{
    public function __construct(
        protected Filter $filter,
        protected mixed $value,
        protected bool $not = false,
    ) {}

    /**
     * Apply the filtering to the current Eloquent builder.
     */
    public function filter(Builder $builder): Builder
    {
        return $builder->{$this->not ? 'whereNotIn' : 'whereIn'}(
            $builder->qualifyColumn($this->filter->getColumn()),
            $this->value
        );
    }
}
