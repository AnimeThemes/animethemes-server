<?php

declare(strict_types=1);

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Sort\FieldCriteria;
use App\Http\Api\Criteria\Sort\RandomCriteria;
use App\Http\Api\Criteria\Sort\RelationCriteria;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\TypeScope;
use Illuminate\Support\Str;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('no criteria by default', function () {
    $parameters = [];

    static::assertEmpty(SortParser::parse($parameters));
});

test('parse random criteria', function () {
    $parameters = [
        SortParser::param() => RandomCriteria::PARAM_VALUE,
    ];

    $criteria = SortParser::parse($parameters)[0];

    static::assertInstanceOf(RandomCriteria::class, $criteria);
});

test('parse relation criteria', function () {
    $parameters = [
        SortParser::param() => collect(fake()->words())->join('.'),
    ];

    $criteria = SortParser::parse($parameters)[0];

    static::assertInstanceOf(RelationCriteria::class, $criteria);
});

test('parse field criteria', function () {
    $parameters = [
        SortParser::param() => fake()->word(),
    ];

    $criteria = SortParser::parse($parameters)[0];

    static::assertInstanceOf(FieldCriteria::class, $criteria);
});

test('parse criteria field', function () {
    $field = fake()->word();

    $parameters = [
        SortParser::param() => $field,
    ];

    $criteria = SortParser::parse($parameters)[0];

    static::assertEquals($field, $criteria->getField());
});

test('parse default direction', function () {
    $parameters = [
        SortParser::param() => fake()->word(),
    ];

    $criteria = SortParser::parse($parameters)[0];

    static::assertTrue(
        $criteria instanceof FieldCriteria
        && $criteria->getDirection() === Direction::ASCENDING
    );
});

test('parse descending direction', function () {
    $field = Str::of('-')->append(fake()->word())->__toString();

    $parameters = [
        SortParser::param() => $field,
    ];

    $criteria = SortParser::parse($parameters)[0];

    static::assertTrue(
        $criteria instanceof FieldCriteria
        && $criteria->getDirection() === Direction::DESCENDING
    );
});

test('parse global scope', function () {
    $parameters = [
        SortParser::param() => fake()->word(),
    ];

    $criteria = SortParser::parse($parameters)[0];

    static::assertInstanceOf(GlobalScope::class, $criteria->getScope());
});

test('parse type scope', function () {
    $type = Str::singular(fake()->word());

    $parameters = [
        SortParser::param() => [
            $type => fake()->word(),
        ],
    ];

    $criteria = SortParser::parse($parameters)[0];

    $scope = $criteria->getScope();

    static::assertTrue($scope instanceof TypeScope && $scope->getType() === $type);
});
