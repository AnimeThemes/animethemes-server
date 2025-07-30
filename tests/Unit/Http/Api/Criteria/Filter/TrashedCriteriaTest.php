<?php

declare(strict_types=1);

use App\Http\Api\Criteria\Filter\TrashedCriteria;
use App\Http\Api\Scope\GlobalScope;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('field', function () {
    $criteria = TrashedCriteria::make(new GlobalScope(), TrashedCriteria::PARAM_VALUE, fake()->word());

    $this->assertEquals(TrashedCriteria::PARAM_VALUE, $criteria->getField());
});
