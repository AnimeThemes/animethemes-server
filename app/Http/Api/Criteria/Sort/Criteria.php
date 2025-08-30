<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Sort;

use App\Http\Api\Scope\Scope;
use App\Http\Api\Sort\Sort;
use Illuminate\Database\Eloquent\Builder;

abstract class Criteria
{
    public function __construct(protected readonly Scope $scope, protected readonly string $field) {}

    /**
     * Get the scope of the criteria.
     */
    public function getScope(): Scope
    {
        return $this->scope;
    }

    /**
     * Get the criteria field.
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Determine if this sort should be applied.
     */
    public function shouldSort(Sort $sort, Scope $scope): bool
    {
        // Don't apply sort if key does not match
        if ($this->getField() !== $sort->getKey()) {
            return false;
        }

        // Don't apply sort if scope does not match
        if (! $this->getScope()->isWithinScope($scope)) {
            return false;
        }

        return true;
    }

    /**
     * @param  Builder  $builder
     * @return Builder
     */
    abstract public function sort(Builder $builder, Sort $sort): Builder;
}
