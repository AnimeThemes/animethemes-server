<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Sort;

use App\Http\Api\Scope\Scope;
use App\Http\Api\Sort\Sort;
use Illuminate\Database\Eloquent\Builder;

class RandomCriteria extends Criteria
{
    final public const string PARAM_VALUE = 'random';

    public function __construct(Scope $scope)
    {
        parent::__construct($scope, self::PARAM_VALUE);
    }

    public function sort(Builder $builder, Sort $sort): Builder
    {
        return $builder->inRandomOrder();
    }
}
