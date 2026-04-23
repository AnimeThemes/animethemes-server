<?php

declare(strict_types=1);

use App\Http\Api\Criteria\Field\Criteria;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

uses(WithFaker::class);

test('is allowed field', function (): void {
    $fields = collect(fake()->words(fake()->randomDigitNotNull()));

    $criteria = new Criteria(fake()->word(), $fields);

    $this->assertTrue($criteria->isAllowedField($fields->random()));
});

test('is not allowed', function (): void {
    $fields = collect(fake()->words(fake()->randomDigitNotNull()));

    $criteria = new Criteria(fake()->word(), $fields);

    $this->assertFalse($criteria->isAllowedField(Str::random()));
});
