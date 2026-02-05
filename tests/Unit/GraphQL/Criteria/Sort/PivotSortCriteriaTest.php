<?php

declare(strict_types=1);

use App\Enums\GraphQL\Sort\SortDirection;
use App\GraphQL\Criteria\Sort\PivotSortCriteria;
use App\GraphQL\Sort\Sort;

it('formats for asc', function () {
    $criteria = new class(new Sort(fake()->word()), SortDirection::ASC) extends PivotSortCriteria {};

    $this->assertStringStartsWith('PIVOT_', $criteria->__toString());
    $this->assertStringEndsNotWith('_DESC', $criteria->__toString());
});

it('formats for desc', function () {
    $criteria = new class(new Sort(fake()->word()), SortDirection::DESC) extends PivotSortCriteria {};

    $this->assertStringStartsWith('PIVOT_', $criteria->__toString());
    $this->assertStringEndsWith('_DESC', $criteria->__toString());
});
