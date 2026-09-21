<?php

declare(strict_types=1);

use App\Http\Api\Criteria\Field\Criteria;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

pest()->use(WithFaker::class);

test('is allowed field', function (): void {
    $fields = collect(fake()->words(fake()->randomDigitNotNull()));

    $criteria = new Criteria(fake()->word(), $fields);

    expect($criteria->isAllowedField($fields->random()))->toBeTrue();
});

test('is not allowed', function (): void {
    $fields = collect(fake()->words(fake()->randomDigitNotNull()));

    $criteria = new Criteria(fake()->word(), $fields);

    expect($criteria->isAllowedField(Str::random()))->toBeFalse();
});
