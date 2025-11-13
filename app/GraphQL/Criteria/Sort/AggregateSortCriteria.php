<?php

declare(strict_types=1);

namespace App\GraphQL\Criteria\Sort;

use Exception;
use Illuminate\Database\Eloquent\Builder;

class AggregateSortCriteria extends SortCriteria
{
    /**
     * Apply the ordering to the current Eloquent builder.
     */
    public function sort(Builder $builder): Builder
    {
        try {
            /** @phpstan-ignore-next-line */
            $relation = $this->field->{'relation'}();
        } catch (Exception) {
            throw new Exception("'relation' method is required for the aggregate sort type.");
        }

        $builder->withAggregate([
            "$relation as {$relation}_value" => function ($query): void {
                $query->orderBy('value', $this->direction->value);
            },
        ], 'value');

        return $builder->orderBy("{$relation}_value", $this->direction->value);
    }
}
