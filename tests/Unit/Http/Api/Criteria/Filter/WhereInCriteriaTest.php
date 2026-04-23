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

uses(WithFaker::class);

test('field', function (): void {
    $field = fake()->word();

    $criteria = WhereInCriteria::make(new GlobalScope(), $field, fake()->word());

    $this->assertEquals($field, $criteria->getField());
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

    $this->assertNull($criteria->getComparisonOperator());
});

test('default logical operator', function (): void {
    $criteria = WhereInCriteria::make(new GlobalScope(), fake()->word(), fake()->word());

    $this->assertEquals(BinaryLogicalOperator::AND, $criteria->getLogicalOperator());
});

test('logical operator', function (): void {
    $operator = Arr::random(BinaryLogicalOperator::cases());

    $filterParam = Str::of(fake()->word())->append(Criteria::PARAM_SEPARATOR)->append($operator->name)->__toString();

    $criteria = WhereInCriteria::make(new GlobalScope(), $filterParam, fake()->word());

    $this->assertEquals($operator, $criteria->getLogicalOperator());
});

test('default unary operator', function (): void {
    $criteria = WhereInCriteria::make(new GlobalScope(), fake()->word(), fake()->word());

    $this->assertFalse($criteria->not());
});

test('unary operator', function (): void {
    $filterParam = Str::of(fake()->word())
        ->append(Criteria::PARAM_SEPARATOR)
        ->append(fake()->word())
        ->append(Criteria::PARAM_SEPARATOR)
        ->append(UnaryLogicalOperator::NOT->value)
        ->__toString();

    $criteria = WhereInCriteria::make(new GlobalScope(), $filterParam, fake()->word());

    $this->assertTrue($criteria->not());
});
