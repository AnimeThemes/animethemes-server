<?php

declare(strict_types=1);

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Criteria\Filter\Criteria;
use App\Http\Api\Criteria\Filter\WhereCriteria;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

pest()->use(WithFaker::class);

test('field', function (): void {
    $field = fake()->word();

    $criteria = WhereCriteria::make(new GlobalScope(), $field, fake()->word());

    expect($criteria->getField())->toEqual($field);
});

test('default comparison operator', function (): void {
    $criteria = WhereCriteria::make(new GlobalScope(), fake()->word(), fake()->word());

    expect($criteria->getComparisonOperator())->toEqual(ComparisonOperator::EQ);
});

test('comparison operator', function (): void {
    $operator = Arr::random(ComparisonOperator::cases());

    $filterParam = Str::of(fake()->word())->append(Criteria::PARAM_SEPARATOR)->append($operator->name)->__toString();

    $criteria = WhereCriteria::make(new GlobalScope(), $filterParam, fake()->word());

    expect($criteria->getComparisonOperator())->toEqual($operator);
});

test('default logical operator', function (): void {
    $criteria = WhereCriteria::make(new GlobalScope(), fake()->word(), fake()->word());

    expect($criteria->getLogicalOperator())->toEqual(BinaryLogicalOperator::AND);
});

test('logical operator', function (): void {
    $operator = Arr::random(BinaryLogicalOperator::cases());

    $filterParam = Str::of(fake()->word())->append(Criteria::PARAM_SEPARATOR)->append($operator->name)->__toString();

    $criteria = WhereCriteria::make(new GlobalScope(), $filterParam, fake()->word());

    expect($criteria->getLogicalOperator())->toEqual($operator);
});
