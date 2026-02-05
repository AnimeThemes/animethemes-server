<?php

declare(strict_types=1);

use App\Enums\GraphQL\Sort\SortDirection;
use App\GraphQL\Criteria\Sort\SortCriteria;
use App\GraphQL\Sort\Sort;
use Illuminate\Database\Eloquent\Builder;

it('formats for asc', function () {
    $criteria = new class(new Sort(fake()->word()), SortDirection::ASC) extends SortCriteria
    {
        public function sort(Builder $builder): Builder
        {
            return $builder;
        }
    };

    $this->assertStringEndsNotWith('_DESC', $criteria->__toString());
});

it('formats for desc', function () {
    $criteria = new class(new Sort(fake()->word()), SortDirection::DESC) extends SortCriteria
    {
        public function sort(Builder $builder): Builder
        {
            return $builder;
        }
    };

    $this->assertStringEndsWith('_DESC', $criteria->__toString());
});
