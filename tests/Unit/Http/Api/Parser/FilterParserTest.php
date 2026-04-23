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

uses(WithFaker::class);

test('no criteria by default', function (): void {
    $parameters = [];

    $this->assertEmpty(FilterParser::parse($parameters));
});

test('parse trashed criteria', function (): void {
    $parameters = [
        FilterParser::param() => [
            TrashedCriteria::PARAM_VALUE => fake()->word(),
        ],
    ];

    $criteria = FilterParser::parse($parameters)[0];

    $this->assertInstanceOf(TrashedCriteria::class, $criteria);
});

test('parse where in criteria', function (): void {
    $fields = collect(fake()->words());

    $parameters = [
        FilterParser::param() => [
            fake()->word() => $fields->join(','),
        ],
    ];

    $criteria = FilterParser::parse($parameters)[0];

    $this->assertInstanceOf(WhereInCriteria::class, $criteria);
});

test('parse has criteria', function (): void {
    $parameters = [
        FilterParser::param() => [
            HasCriteria::PARAM_VALUE => fake()->word(),
        ],
    ];

    $criteria = FilterParser::parse($parameters)[0];

    $this->assertInstanceOf(HasCriteria::class, $criteria);
});

test('parse where criteria', function (): void {
    $parameters = [
        FilterParser::param() => [
            fake()->word() => fake()->word(),
        ],
    ];

    $criteria = FilterParser::parse($parameters)[0];

    $this->assertInstanceOf(WhereCriteria::class, $criteria);
});

test('parse global scope', function (): void {
    $parameters = [
        FilterParser::param() => [
            fake()->word() => fake()->word(),
        ],
    ];

    $criteria = FilterParser::parse($parameters)[0];

    $this->assertInstanceOf(GlobalScope::class, $criteria->getScope());
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

    $this->assertTrue($scope instanceof TypeScope && $scope->getType() === $type);
});
