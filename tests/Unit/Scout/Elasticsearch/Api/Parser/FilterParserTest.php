<?php

declare(strict_types=1);

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Criteria\Filter\Expression;
use App\Http\Api\Criteria\Filter\HasCriteria;
use App\Http\Api\Criteria\Filter\Predicate;
use App\Http\Api\Criteria\Filter\TrashedCriteria;
use App\Http\Api\Criteria\Filter\WhereCriteria as BaseWhereCriteria;
use App\Http\Api\Criteria\Filter\WhereInCriteria as BaseWhereInCriteria;
use App\Http\Api\Scope\GlobalScope;
use App\Scout\Elasticsearch\Api\Criteria\Filter\WhereCriteria;
use App\Scout\Elasticsearch\Api\Criteria\Filter\WhereInCriteria;
use App\Scout\Elasticsearch\Api\Parser\FilterParser;
use Illuminate\Support\Arr;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('where criteria', function () {
    $expression = new Expression(fake()->word());

    $comparisonOperator = Arr::random(ComparisonOperator::cases());

    $predicate = new Predicate(fake()->word(), $comparisonOperator, $expression);

    $logicalOperator = Arr::random(BinaryLogicalOperator::cases());

    $criteria = new BaseWhereCriteria($predicate, $logicalOperator, new GlobalScope());

    static::assertInstanceOf(WhereCriteria::class, FilterParser::parse($criteria));
});

test('where in criteria', function () {
    $expression = new Expression(fake()->word());

    $comparisonOperator = Arr::random(ComparisonOperator::cases());

    $predicate = new Predicate(fake()->word(), $comparisonOperator, $expression);

    $criteria = new BaseWhereInCriteria(
        $predicate,
        Arr::random(BinaryLogicalOperator::cases()),
        fake()->boolean(),
        new GlobalScope()
    );

    static::assertInstanceOf(WhereInCriteria::class, FilterParser::parse($criteria));
});

test('has criteria', function () {
    $expression = new Expression(fake()->word());

    $comparisonOperator = Arr::random(ComparisonOperator::cases());

    $predicate = new Predicate(fake()->word(), $comparisonOperator, $expression);

    $criteria = new HasCriteria(
        $predicate,
        Arr::random(BinaryLogicalOperator::cases()),
        new GlobalScope(),
        fake()->randomDigitNotNull()
    );

    static::assertNull(FilterParser::parse($criteria));
});

test('trashed criteria', function () {
    $expression = new Expression(fake()->word());

    $comparisonOperator = Arr::random(ComparisonOperator::cases());

    $predicate = new Predicate(fake()->word(), $comparisonOperator, $expression);

    $criteria = new TrashedCriteria(
        $predicate,
        Arr::random(BinaryLogicalOperator::cases()),
        new GlobalScope()
    );

    static::assertNull(FilterParser::parse($criteria));
});
