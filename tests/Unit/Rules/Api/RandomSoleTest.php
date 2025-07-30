<?php

declare(strict_types=1);

use App\Http\Api\Criteria\Sort\RandomCriteria;
use App\Rules\Api\RandomSoleRule;
use Illuminate\Support\Facades\Validator;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('fails if random is not sole sort', function () {
    $sorts = fake()->words(fake()->randomDigitNotNull());

    $sorts[] = RandomCriteria::PARAM_VALUE;

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => implode(',', $sorts)],
        [$attribute => new RandomSoleRule()]
    );

    static::assertFalse($validator->passes());
});

test('passes if random is not included', function () {
    $sorts = fake()->words(fake()->randomDigitNotNull());

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => implode(',', $sorts)],
        [$attribute => new RandomSoleRule()]
    );

    static::assertTrue($validator->passes());
});

test('passes if random is sole sort', function () {
    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => RandomCriteria::PARAM_VALUE],
        [$attribute => new RandomSoleRule()]
    );

    static::assertTrue($validator->passes());
});
