<?php

declare(strict_types=1);

use App\Enums\GraphQL\Sort\SortDirection;
use App\GraphQL\Criteria\Sort\RelationSortCriteria;
use App\GraphQL\Sort\Sort;
use Illuminate\Support\Str;

it('formats for asc', function () {
    $relation = fake()->word();

    $criteria = new class(new Sort(fake()->word()), $relation, SortDirection::ASC) extends RelationSortCriteria {};

    $this->assertStringStartsWith(Str::upper($relation), $criteria->__toString());
    $this->assertStringEndsNotWith('_DESC', $criteria->__toString());
});

it('formats for desc', function () {
    $relation = fake()->word();

    $criteria = new class(new Sort(fake()->word()), $relation, SortDirection::DESC) extends RelationSortCriteria {};

    $this->assertStringStartsWith(Str::upper($relation), $criteria->__toString());
    $this->assertStringEndsWith('_DESC', $criteria->__toString());
});
