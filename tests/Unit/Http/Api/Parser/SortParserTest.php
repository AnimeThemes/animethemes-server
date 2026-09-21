<?php

declare(strict_types=1);

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Sort\FieldCriteria;
use App\Http\Api\Criteria\Sort\RandomCriteria;
use App\Http\Api\Criteria\Sort\RelationCriteria;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\TypeScope;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

pest()->use(WithFaker::class);

test('no criteria by default', function (): void {
    $parameters = [];

    expect(SortParser::parse($parameters))->toBeEmpty();
});

test('parse random criteria', function (): void {
    $parameters = [
        SortParser::param() => RandomCriteria::PARAM_VALUE,
    ];

    $criteria = SortParser::parse($parameters)[0];

    expect($criteria)->toBeInstanceOf(RandomCriteria::class);
});

test('parse relation criteria', function (): void {
    $parameters = [
        SortParser::param() => collect(fake()->words())->join('.'),
    ];

    $criteria = SortParser::parse($parameters)[0];

    expect($criteria)->toBeInstanceOf(RelationCriteria::class);
});

test('parse field criteria', function (): void {
    $parameters = [
        SortParser::param() => fake()->word(),
    ];

    $criteria = SortParser::parse($parameters)[0];

    expect($criteria)->toBeInstanceOf(FieldCriteria::class);
});

test('parse criteria field', function (): void {
    $field = fake()->word();

    $parameters = [
        SortParser::param() => $field,
    ];

    $criteria = SortParser::parse($parameters)[0];

    expect($criteria->getField())->toEqual($field);
});

test('parse default direction', function (): void {
    $parameters = [
        SortParser::param() => fake()->word(),
    ];

    $criteria = SortParser::parse($parameters)[0];

    expect($criteria instanceof FieldCriteria
    && $criteria->getDirection() === Direction::ASCENDING)->toBeTrue();
});

test('parse descending direction', function (): void {
    $field = Str::of('-')->append(fake()->word())->__toString();

    $parameters = [
        SortParser::param() => $field,
    ];

    $criteria = SortParser::parse($parameters)[0];

    expect($criteria instanceof FieldCriteria
    && $criteria->getDirection() === Direction::DESCENDING)->toBeTrue();
});

test('parse global scope', function (): void {
    $parameters = [
        SortParser::param() => fake()->word(),
    ];

    $criteria = SortParser::parse($parameters)[0];

    expect($criteria->getScope())->toBeInstanceOf(GlobalScope::class);
});

test('parse type scope', function (): void {
    $type = Str::singular('posts');

    $parameters = [
        SortParser::param() => [
            $type => fake()->word(),
        ],
    ];

    $criteria = SortParser::parse($parameters)[0];

    $scope = $criteria->getScope();

    expect($scope instanceof TypeScope && $scope->getType() === $type)->toBeTrue();
});
