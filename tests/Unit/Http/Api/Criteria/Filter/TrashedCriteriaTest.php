<?php

declare(strict_types=1);

use App\Http\Api\Criteria\Filter\TrashedCriteria;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('field', function (): void {
    $criteria = TrashedCriteria::make(new GlobalScope(), TrashedCriteria::PARAM_VALUE, fake()->word());

    expect($criteria->getField())->toEqual(TrashedCriteria::PARAM_VALUE);
});
