<?php

declare(strict_types=1);

use App\Http\Api\Criteria\Field\Criteria;
use App\Http\Api\Parser\FieldParser;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('no criteria by default', function (): void {
    $parameters = [];

    expect(FieldParser::parse($parameters))->toBeEmpty();
});

test('parse criteria', function (): void {
    $fields = collect(fake()->words(fake()->randomDigitNotNull()));

    $parameters = [
        FieldParser::param() => [
            fake()->word() => $fields->join(','),
        ],
    ];

    $criteria = FieldParser::parse($parameters)[0];

    expect($criteria)->toBeInstanceOf(Criteria::class);
});

test('parse type', function (): void {
    $type = fake()->word();

    $fields = collect(fake()->words(fake()->randomDigitNotNull()));

    $parameters = [
        FieldParser::param() => [
            $type => $fields->join(','),
        ],
    ];

    $criteria = FieldParser::parse($parameters)[0];

    expect($criteria->getType())->toEqual($type);
});

test('parse fields', function (): void {
    $fields = fake()->words(fake()->randomDigitNotNull());

    $parameters = [
        FieldParser::param() => [
            fake()->word() => collect($fields)->join(','),
        ],
    ];

    $criteria = FieldParser::parse($parameters)[0];

    expect($criteria->getFields()->all())->toEqual($fields);
});
