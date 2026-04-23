<?php

declare(strict_types=1);

use App\Http\Api\Criteria\Field\Criteria;
use App\Http\Api\Parser\FieldParser;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('no criteria by default', function (): void {
    $parameters = [];

    $this->assertEmpty(FieldParser::parse($parameters));
});

test('parse criteria', function (): void {
    $fields = collect(fake()->words(fake()->randomDigitNotNull()));

    $parameters = [
        FieldParser::param() => [
            fake()->word() => $fields->join(','),
        ],
    ];

    $criteria = FieldParser::parse($parameters)[0];

    $this->assertInstanceOf(Criteria::class, $criteria);
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

    $this->assertEquals($type, $criteria->getType());
});

test('parse fields', function (): void {
    $fields = fake()->words(fake()->randomDigitNotNull());

    $parameters = [
        FieldParser::param() => [
            fake()->word() => collect($fields)->join(','),
        ],
    ];

    $criteria = FieldParser::parse($parameters)[0];

    $this->assertEquals($fields, $criteria->getFields()->all());
});
