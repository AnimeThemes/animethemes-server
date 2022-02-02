<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Sort;

use App\Http\Api\Sort\Sort;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Criteria.
 */
abstract class Criteria
{
    /**
     * Create a new criteria instance.
     *
     * @param  string  $field
     */
    public function __construct(protected string $field)
    {
    }

    /**
     * Get the criteria field.
     *
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Determine if this sort should be applied.
     *
     * @param  Sort  $sort
     * @return bool
     */
    public function shouldSort(Sort $sort): bool
    {
        // Apply sort if key matches
        return $this->getField() === $sort->getKey();
    }

    /**
     * Apply criteria to builder.
     *
     * @param Builder $builder
     * @param Sort $sort
     * @return Builder
     */
    abstract public function sort(Builder $builder, Sort $sort): Builder;
}
