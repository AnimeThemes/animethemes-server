<?php

declare(strict_types=1);

use App\Http\Api\Criteria\Field\Criteria;
use App\Http\Api\Parser\FieldParser;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('no criteria by default', function () {
    $parameters = [];

    $this->assertEmpty(FieldParser::parse($parameters));
});

test('parse criteria', function () {
    $fields = collect(fake()->words(fake()->randomDigitNotNull()));

    $parameters = [
        FieldParser::param() => [
            fake()->word() => $fields->join(','),
        ],
    ];

    $criteria = FieldParser::parse($parameters)[0];

    $this->assertInstanceOf(Criteria::class, $criteria);
});

test('parse type', function () {
    $type = fake()->word();

    $fields = collect(fake()->words(fake()->randomDigitNotNull()));

    $parameters = [
        FieldParser::param() => [
            $type => $fields->join(','),
        ],
    ];

    $criteria = FieldParser::parse($parameters)[0];

    $this->assertEquals($type, $criteria->getType());
});

test('parse fields', function () {
    $fields = fake()->words(fake()->randomDigitNotNull());

    $parameters = [
        FieldParser::param() => [
            fake()->word() => collect($fields)->join(','),
        ],
    ];

    $criteria = FieldParser::parse($parameters)[0];

    $this->assertEquals($fields, $criteria->getFields()->all());
});
