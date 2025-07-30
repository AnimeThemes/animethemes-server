<?php

declare(strict_types=1);

use App\Http\Api\Criteria\Field\Criteria;
use Illuminate\Support\Str;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('is allowed field', function () {
    $fields = collect(fake()->words(fake()->randomDigitNotNull()));

    $criteria = new Criteria(fake()->word(), $fields);

    $this->assertTrue($criteria->isAllowedField($fields->random()));
});

test('is not allowed', function () {
    $fields = collect(fake()->words(fake()->randomDigitNotNull()));

    $criteria = new Criteria(fake()->word(), $fields);

    $this->assertFalse($criteria->isAllowedField(Str::random()));
});
