<?php

declare(strict_types=1);

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Enums\Http\Api\Filter\UnaryLogicalOperator;
use App\Http\Api\Criteria\Filter\Criteria;
use App\Http\Api\Criteria\Filter\WhereInCriteria;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

pest()->use(WithFaker::class);

test('field', function (): void {
    $field = fake()->word();

    $criteria = WhereInCriteria::make(new GlobalScope(), $field, fake()->word());

    expect($criteria->getField())->toEqual($field);
});

test('comparison operator', function (): void {
    $operator = Arr::random(ComparisonOperator::cases());

    $filterParam = Str::of(fake()->word())
        ->append('.')
        ->append(fake()->word())
        ->append('.')
        ->append($operator->name)
        ->__toString();

    $criteria = WhereInCriteria::make(new GlobalScope(), $filterParam, fake()->word());

    expect($criteria->getComparisonOperator())->toBeNull();
});

test('default logical operator', function (): void {
    $criteria = WhereInCriteria::make(new GlobalScope(), fake()->word(), fake()->word());

    expect($criteria->getLogicalOperator())->toEqual(BinaryLogicalOperator::AND);
});

test('logical operator', function (): void {
    $operator = Arr::random(BinaryLogicalOperator::cases());

    $filterParam = Str::of(fake()->word())->append(Criteria::PARAM_SEPARATOR)->append($operator->name)->__toString();

    $criteria = WhereInCriteria::make(new GlobalScope(), $filterParam, fake()->word());

    expect($criteria->getLogicalOperator())->toEqual($operator);
});

test('default unary operator', function (): void {
    $criteria = WhereInCriteria::make(new GlobalScope(), fake()->word(), fake()->word());

    expect($criteria->not())->toBeFalse();
});

test('unary operator', function (): void {
    $filterParam = Str::of(fake()->word())
        ->append(Criteria::PARAM_SEPARATOR)
        ->append(fake()->word())
        ->append(Criteria::PARAM_SEPARATOR)
        ->append(UnaryLogicalOperator::NOT->value)
        ->__toString();

    $criteria = WhereInCriteria::make(new GlobalScope(), $filterParam, fake()->word());

    expect($criteria->not())->toBeTrue();
});
