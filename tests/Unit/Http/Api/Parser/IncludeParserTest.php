<?php

declare(strict_types=1);

use App\Http\Api\Criteria\Include\Criteria;
use App\Http\Api\Criteria\Include\ResourceCriteria;
use App\Http\Api\Parser\IncludeParser;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('no criteria by default', function (): void {
    $parameters = [];

    $this->assertEmpty(IncludeParser::parse($parameters));
});

test('parse criteria', function (): void {
    $fields = collect(fake()->words(fake()->randomDigitNotNull()));

    $parameters = [
        IncludeParser::param() => $fields->join(','),
    ];

    $criteria = IncludeParser::parse($parameters)[0];

    $this->assertInstanceOf(Criteria::class, $criteria);
});

test('parse criteria paths', function (): void {
    $fields = fake()->words(fake()->randomDigitNotNull());

    $parameters = [
        IncludeParser::param() => collect($fields)->join(','),
    ];

    $criteria = IncludeParser::parse($parameters)[0];

    $this->assertEquals(collect($fields)->unique()->all(), $criteria->getPaths()->all());
});

test('parse resource criteria', function (): void {
    $fields = collect(fake()->words(fake()->randomDigitNotNull()));

    $parameters = [
        IncludeParser::param() => [
            fake()->word() => $fields->join(','),
        ],
    ];

    $criteria = IncludeParser::parse($parameters)[0];

    $this->assertInstanceOf(ResourceCriteria::class, $criteria);
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

    $this->assertTrue(
        $criteria instanceof ResourceCriteria
        && $criteria->getType() === $type
    );
});

test('parse resource criteria paths', function (): void {
    $fields = collect(fake()->words(fake()->randomDigitNotNull()));

    $parameters = [
        IncludeParser::param() => [
            fake()->word() => $fields->join(','),
        ],
    ];

    $criteria = IncludeParser::parse($parameters)[0];

    $this->assertEquals($fields->unique()->all(), $criteria->getPaths()->all());
});
