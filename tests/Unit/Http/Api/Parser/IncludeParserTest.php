<?php

declare(strict_types=1);

use App\Http\Api\Criteria\Include\Criteria;
use App\Http\Api\Criteria\Include\ResourceCriteria;
use App\Http\Api\Parser\IncludeParser;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('no criteria by default', function (): void {
    $parameters = [];

    expect(IncludeParser::parse($parameters))->toBeEmpty();
});

test('parse criteria', function (): void {
    $fields = collect(fake()->words(fake()->randomDigitNotNull()));

    $parameters = [
        IncludeParser::param() => $fields->join(','),
    ];

    $criteria = IncludeParser::parse($parameters)[0];

    expect($criteria)->toBeInstanceOf(Criteria::class);
});

test('parse criteria paths', function (): void {
    $fields = fake()->words(fake()->randomDigitNotNull());

    $parameters = [
        IncludeParser::param() => collect($fields)->join(','),
    ];

    $criteria = IncludeParser::parse($parameters)[0];

    expect($criteria->getPaths()->all())->toEqual(collect($fields)->unique()->all());
});

test('parse resource criteria', function (): void {
    $fields = collect(fake()->words(fake()->randomDigitNotNull()));

    $parameters = [
        IncludeParser::param() => [
            fake()->word() => $fields->join(','),
        ],
    ];

    $criteria = IncludeParser::parse($parameters)[0];

    expect($criteria)->toBeInstanceOf(ResourceCriteria::class);
});

test('parse resource criteria type', function (): void {
    $type = fake()->word();

    $fields = collect(fake()->words(fake()->randomDigitNotNull()));

    $parameters = [
        IncludeParser::param() => [
            $type => $fields->join(','),
        ],
    ];

    $criteria = IncludeParser::parse($parameters)[0];

    expect($criteria instanceof ResourceCriteria
    && $criteria->getType() === $type)->toBeTrue();
});

test('parse resource criteria paths', function (): void {
    $fields = collect(fake()->words(fake()->randomDigitNotNull()));

    $parameters = [
        IncludeParser::param() => [
            fake()->word() => $fields->join(','),
        ],
    ];

    $criteria = IncludeParser::parse($parameters)[0];

    expect($criteria->getPaths()->all())->toEqual($fields->unique()->all());
});
