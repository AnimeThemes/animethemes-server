<?php

declare(strict_types=1);

use App\GraphQL\Filter\IntFilter;
use Tests\Unit\GraphQL\Criteria\Filter\FakeCriteria;

it('converts validated integers', function () {
    $intValue = fake()->year();

    $filter = new IntFilter(fake()->word());

    $criteria = new FakeCriteria($filter, $intValue);

    $filterValues = $filter->getFilterValues($criteria->getFilterValues());

    $this->assertEquals(intval($intValue), $filterValues[0]);
});
