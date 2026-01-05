<?php

declare(strict_types=1);

use App\GraphQL\Filter\FloatFilter;
use Tests\Unit\GraphQL\Criteria\Filter\FakeCriteria;

it('converts validated floats', function () {
    $floatValue = fake()->randomFloat();

    $filter = new FloatFilter(fake()->word());

    $criteria = new FakeCriteria($filter, $floatValue);

    $filterValues = $filter->getFilterValues($criteria->getFilterValues());

    $this->assertEqualsWithDelta($floatValue, $filterValues[0], 0.0001);
});
