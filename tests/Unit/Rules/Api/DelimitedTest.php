<?php

declare(strict_types=1);

use App\Rules\Api\DelimitedRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;

pest()->use(WithFaker::class);

test('passes if all values pass', function (): void {
    $attribute = fake()->word();

    $values = collect(fake()->words())->unique();

    $validator = Validator::make(
        [$attribute => $values->implode(',')],
        [$attribute => new DelimitedRule(['required', 'string'])]
    );

    expect($validator->passes())->toBeTrue();
});

test('fails for duplicate values', function (): void {
    $attribute = fake()->word();

    $duplicate = fake()->word();

    $values = collect([$duplicate, fake()->word(), $duplicate]);

    $validator = Validator::make(
        [$attribute => $values->implode(',')],
        [$attribute => new DelimitedRule(['required', 'string'])]
    );

    expect($validator->passes())->toBeFalse();
});

test('fails for invalid value', function (): void {
    $attribute = fake()->word();

    $values = collect([fake()->randomDigitNotNull(), fake()->word(), fake()->randomDigitNotNull()]);

    $validator = Validator::make(
        [$attribute => $values->implode(',')],
        [$attribute => new DelimitedRule(['required', 'integer'])]
    );

    expect($validator->passes())->toBeFalse();
});

test('validates empty values', function (): void {
    $attribute = fake()->word();

    $values = collect(array_merge(fake()->words(), ['']));

    $validator = Validator::make(
        [$attribute => $values->implode(',')],
        [$attribute => new DelimitedRule(['required', 'string'])]
    );

    expect($validator->passes())->toBeFalse();
});
