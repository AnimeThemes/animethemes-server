<?php

declare(strict_types=1);

use App\Rules\Api\IsValidBoolean;
use Illuminate\Support\Facades\Validator;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('passes if boolean', function () {
    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => fake()->boolean()],
        [$attribute => new IsValidBoolean()]
    );

    $this->assertTrue($validator->passes());
});

test('passes if boolean string', function () {
    $booleanString = fake()->boolean() ? 'true' : 'false';

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $booleanString],
        [$attribute => new IsValidBoolean()]
    );

    $this->assertTrue($validator->passes());
});

test('passes if boolean integer', function () {
    $booleanInteger = fake()->boolean() ? 1 : 0;

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $booleanInteger],
        [$attribute => new IsValidBoolean()]
    );

    $this->assertTrue($validator->passes());
});

test('passes if boolean checkbox', function () {
    $booleanCheckbox = fake()->boolean() ? 'on' : 'off';

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $booleanCheckbox],
        [$attribute => new IsValidBoolean()]
    );

    $this->assertTrue($validator->passes());
});

test('fails if string', function () {
    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => fake()->word()],
        [$attribute => new IsValidBoolean()]
    );

    $this->assertFalse($validator->passes());
});

test('fails if number', function () {
    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => fake()->numberBetween(2, 9)],
        [$attribute => new IsValidBoolean()]
    );

    $this->assertFalse($validator->passes());
});
