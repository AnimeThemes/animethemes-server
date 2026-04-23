<?php

declare(strict_types=1);

use App\Rules\Api\DelimitedRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;

uses(WithFaker::class);

test('passes if all values pass', function (): void {
    $attribute = fake()->word();

    $values = collect(fake()->words())->unique();

    $validator = Validator::make(
        [$attribute => $values->implode(',')],
        [$attribute => new DelimitedRule(['required', 'string'])]
    );

    $this->assertTrue($validator->passes());
});

test('fails for duplicate values', function (): void {
    $attribute = fake()->word();

    $duplicate = fake()->word();

    $values = collect([$duplicate, fake()->word(), $duplicate]);

    $validator = Validator::make(
        [$attribute => $values->implode(',')],
        [$attribute => new DelimitedRule(['required', 'string'])]
    );

    $this->assertFalse($validator->passes());
});

test('fails for invalid value', function (): void {
    $attribute = fake()->word();

    $values = collect([fake()->randomDigitNotNull(), fake()->word(), fake()->randomDigitNotNull()]);

    $validator = Validator::make(
        [$attribute => $values->implode(',')],
        [$attribute => new DelimitedRule(['required', 'integer'])]
    );

    $this->assertFalse($validator->passes());
});

test('validates empty values', function (): void {
    $attribute = fake()->word();

    $values = collect(array_merge(fake()->words(), ['']));

    $validator = Validator::make(
        [$attribute => $values->implode(',')],
        [$attribute => new DelimitedRule(['required', 'string'])]
    );

    $this->assertFalse($validator->passes());
});
