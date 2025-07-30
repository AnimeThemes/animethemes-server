<?php

declare(strict_types=1);

use App\Http\Api\Criteria\Include\Criteria;
use App\Http\Api\Criteria\Include\ResourceCriteria;
use App\Http\Api\Parser\IncludeParser;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('no criteria by default', function () {
    $parameters = [];

    $this->assertEmpty(IncludeParser::parse($parameters));
});

test('parse criteria', function () {
    $fields = collect(fake()->words(fake()->randomDigitNotNull()));

    $parameters = [
        IncludeParser::param() => $fields->join(','),
    ];

    $criteria = IncludeParser::parse($parameters)[0];

    $this->assertInstanceOf(Criteria::class, $criteria);
});

test('parse criteria paths', function () {
    $fields = fake()->words(fake()->randomDigitNotNull());

    $parameters = [
        IncludeParser::param() => collect($fields)->join(','),
    ];

    $criteria = IncludeParser::parse($parameters)[0];

    $this->assertEquals($fields, $criteria->getPaths()->all());
});

test('parse resource criteria', function () {
    $fields = collect(fake()->words(fake()->randomDigitNotNull()));

    $parameters = [
        IncludeParser::param() => [
            fake()->word() => $fields->join(','),
        ],
    ];

    $criteria = IncludeParser::parse($parameters)[0];

    $this->assertInstanceOf(ResourceCriteria::class, $criteria);
});

test('parse resource criteria type', function () {
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

test('parse resource criteria paths', function () {
    $fields = fake()->words(fake()->randomDigitNotNull());

    $parameters = [
        IncludeParser::param() => [
            fake()->word() => collect($fields)->join(','),
        ],
    ];

    $criteria = IncludeParser::parse($parameters)[0];

    $this->assertEquals($fields, $criteria->getPaths()->all());
});
