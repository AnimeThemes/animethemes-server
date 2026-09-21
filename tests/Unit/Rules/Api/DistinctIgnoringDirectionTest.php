<?php

declare(strict_types=1);

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Sort\Sort;
use App\Rules\Api\DistinctIgnoringDirectionRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;

pest()->use(WithFaker::class);

test('fails if duplicate sort', function (): void {
    $key = fake()->word();

    $sorts = collect()->pad(fake()->numberBetween(2, 9), $key);

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $sorts->join(',')],
        [$attribute => new DistinctIgnoringDirectionRule()]
    );

    expect($validator->passes())->toBeFalse();
});

test('fails if duplicate sort different direction', function (): void {
    $key = fake()->word();

    $sort = new Sort($key);

    $sorts = [];

    foreach (Direction::cases() as $direction) {
        $sorts[] = $sort->format($direction);
    }

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => implode(',', $sorts)],
        [$attribute => new DistinctIgnoringDirectionRule()]
    );

    expect($validator->passes())->toBeFalse();
});

test('passes if no duplicates', function (): void {
    $sorts = collect(fake()->words(fake()->randomDigitNotNull()))->unique();

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $sorts->join(',')],
        [$attribute => new DistinctIgnoringDirectionRule()]
    );

    expect($validator->passes())->toBeTrue();
});
