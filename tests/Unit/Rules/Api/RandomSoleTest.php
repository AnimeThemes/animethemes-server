<?php

declare(strict_types=1);

use App\Http\Api\Criteria\Sort\RandomCriteria;
use App\Rules\Api\RandomSoleRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;

uses(WithFaker::class);

test('fails if random is not sole sort', function (): void {
    $sorts = fake()->words(fake()->randomDigitNotNull());

    $sorts[] = RandomCriteria::PARAM_VALUE;

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => implode(',', $sorts)],
        [$attribute => new RandomSoleRule()]
    );

    $this->assertFalse($validator->passes());
});

test('passes if random is not included', function (): void {
    $sorts = fake()->words(fake()->randomDigitNotNull());

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => implode(',', $sorts)],
        [$attribute => new RandomSoleRule()]
    );

    $this->assertTrue($validator->passes());
});

test('passes if random is sole sort', function (): void {
    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => RandomCriteria::PARAM_VALUE],
        [$attribute => new RandomSoleRule()]
    );

    $this->assertTrue($validator->passes());
});
