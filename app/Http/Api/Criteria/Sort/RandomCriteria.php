<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Sort;

use App\Http\Api\Scope\Scope;
use App\Http\Api\Sort\Sort;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class RandomSort.
 */
class RandomCriteria extends Criteria
{
    final public const PARAM_VALUE = 'random';

    /**
     * Create a new criteria instance.
     *
     * @param  Scope  $scope
     */
    public function __construct(Scope $scope)
    {
        parent::__construct($scope, self::PARAM_VALUE);
    }

    /**
     * Apply criteria to builder.
     *
     * @param  Builder  $builder
     * @param  Sort  $sort
     * @return Builder
     */
    public function sort(Builder $builder, Sort $sort): Builder
    {
        return $builder->inRandomOrder();
    }
}
