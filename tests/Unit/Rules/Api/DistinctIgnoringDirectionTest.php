<?php

declare(strict_types=1);

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Sort\Sort;
use App\Rules\Api\DistinctIgnoringDirectionRule;
use Illuminate\Support\Facades\Validator;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('fails if duplicate sort', function () {
    $key = fake()->word();

    $sorts = collect()->pad(fake()->numberBetween(2, 9), $key);

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $sorts->join(',')],
        [$attribute => new DistinctIgnoringDirectionRule()]
    );

    $this->assertFalse($validator->passes());
});

test('fails if duplicate sort different direction', function () {
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

    $this->assertFalse($validator->passes());
});

test('passes if no duplicates', function () {
    $sorts = collect(fake()->words(fake()->randomDigitNotNull()))->unique();

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $sorts->join(',')],
        [$attribute => new DistinctIgnoringDirectionRule()]
    );

    $this->assertTrue($validator->passes());
});
