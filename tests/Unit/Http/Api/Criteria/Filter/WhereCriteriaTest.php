<?php

declare(strict_types=1);

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Criteria\Filter\Criteria;
use App\Http\Api\Criteria\Filter\WhereCriteria;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('field', function () {
    $field = fake()->word();

    $criteria = WhereCriteria::make(new GlobalScope(), $field, fake()->word());

    static::assertEquals($field, $criteria->getField());
});

test('default comparison operator', function () {
    $criteria = WhereCriteria::make(new GlobalScope(), fake()->word(), fake()->word());

    static::assertEquals(ComparisonOperator::EQ, $criteria->getComparisonOperator());
});

test('comparison operator', function () {
    $operator = Arr::random(ComparisonOperator::cases());

    $filterParam = Str::of(fake()->word())->append(Criteria::PARAM_SEPARATOR)->append($operator->name)->__toString();

    $criteria = WhereCriteria::make(new GlobalScope(), $filterParam, fake()->word());

    static::assertEquals($operator, $criteria->getComparisonOperator());
});

test('default logical operator', function () {
    $criteria = WhereCriteria::make(new GlobalScope(), fake()->word(), fake()->word());

    static::assertEquals(BinaryLogicalOperator::AND, $criteria->getLogicalOperator());
});

test('logical operator', function () {
    $operator = Arr::random(BinaryLogicalOperator::cases());

    $filterParam = Str::of(fake()->word())->append(Criteria::PARAM_SEPARATOR)->append($operator->name)->__toString();

    $criteria = WhereCriteria::make(new GlobalScope(), $filterParam, fake()->word());

    static::assertEquals($operator, $criteria->getLogicalOperator());
});
