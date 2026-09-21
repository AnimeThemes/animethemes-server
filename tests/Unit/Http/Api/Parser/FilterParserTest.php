<?php

declare(strict_types=1);

use App\Http\Api\Criteria\Filter\HasCriteria;
use App\Http\Api\Criteria\Filter\TrashedCriteria;
use App\Http\Api\Criteria\Filter\WhereCriteria;
use App\Http\Api\Criteria\Filter\WhereInCriteria;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\TypeScope;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

pest()->use(WithFaker::class);

test('no criteria by default', function (): void {
    $parameters = [];

    expect(FilterParser::parse($parameters))->toBeEmpty();
});

test('parse trashed criteria', function (): void {
    $parameters = [
        FilterParser::param() => [
            TrashedCriteria::PARAM_VALUE => fake()->word(),
        ],
    ];

    $criteria = FilterParser::parse($parameters)[0];

    expect($criteria)->toBeInstanceOf(TrashedCriteria::class);
});

test('parse where in criteria', function (): void {
    $fields = collect(fake()->words());

    $parameters = [
        FilterParser::param() => [
            fake()->word() => $fields->join(','),
        ],
    ];

    $criteria = FilterParser::parse($parameters)[0];

    expect($criteria)->toBeInstanceOf(WhereInCriteria::class);
});

test('parse has criteria', function (): void {
    $parameters = [
        FilterParser::param() => [
            HasCriteria::PARAM_VALUE => fake()->word(),
        ],
    ];

    $criteria = FilterParser::parse($parameters)[0];

    expect($criteria)->toBeInstanceOf(HasCriteria::class);
});

test('parse where criteria', function (): void {
    $parameters = [
        FilterParser::param() => [
            fake()->word() => fake()->word(),
        ],
    ];

    $criteria = FilterParser::parse($parameters)[0];

    expect($criteria)->toBeInstanceOf(WhereCriteria::class);
});

test('parse global scope', function (): void {
    $parameters = [
        FilterParser::param() => [
            fake()->word() => fake()->word(),
        ],
    ];

    $criteria = FilterParser::parse($parameters)[0];

    expect($criteria->getScope())->toBeInstanceOf(GlobalScope::class);
});

test('parse type scope', function (): void {
    $type = Str::singular(fake()->word());

    $parameters = [
        FilterParser::param() => [
            $type => [
                fake()->word() => fake()->word(),
            ],
        ],
    ];

    $criteria = FilterParser::parse($parameters)[0];

    $scope = $criteria->getScope();

    expect($scope instanceof TypeScope && $scope->getType() === $type)->toBeTrue();
});
