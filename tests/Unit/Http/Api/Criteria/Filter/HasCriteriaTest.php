<?php

declare(strict_types=1);

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Criteria\Filter\Criteria;
use App\Http\Api\Criteria\Filter\HasCriteria;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

uses(WithFaker::class);

test('field', function (): void {
    $criteria = HasCriteria::make(new GlobalScope(), HasCriteria::PARAM_VALUE, fake()->word());

    $this->assertEquals(HasCriteria::PARAM_VALUE, $criteria->getField());
});

test('default comparison operator', function (): void {
    $criteria = HasCriteria::make(new GlobalScope(), HasCriteria::PARAM_VALUE, fake()->word());

    $this->assertEquals(ComparisonOperator::GTE, $criteria->getComparisonOperator());
});

test('comparison operator', function (): void {
    $operator = Arr::random(ComparisonOperator::cases());

    $filterParam = Str::of(HasCriteria::PARAM_VALUE)->append(Criteria::PARAM_SEPARATOR)->append($operator->name)->__toString();

    $criteria = HasCriteria::make(new GlobalScope(), $filterParam, fake()->word());

    $this->assertEquals($operator, $criteria->getComparisonOperator());
});

test('default count', function (): void {
    $criteria = HasCriteria::make(new GlobalScope(), HasCriteria::PARAM_VALUE, fake()->word());

    $this->assertEquals(1, $criteria->getCount());
});

test('count', function (): void {
    $count = fake()->randomDigitNotNull();

    $filterParam = Str::of(HasCriteria::PARAM_VALUE)->append(Criteria::PARAM_SEPARATOR)->append(strval($count))->__toString();

    $criteria = HasCriteria::make(new GlobalScope(), $filterParam, fake()->word());

    $this->assertEquals($count, $criteria->getCount());
});

test('default logical operator', function (): void {
    $criteria = HasCriteria::make(new GlobalScope(), fake()->word(), fake()->word());

    $this->assertEquals(BinaryLogicalOperator::AND, $criteria->getLogicalOperator());
});

test('logical operator', function (): void {
    $operator = Arr::random(BinaryLogicalOperator::cases());

    $filterParam = Str::of(HasCriteria::PARAM_VALUE)->append(Criteria::PARAM_SEPARATOR)->append($operator->name)->__toString();

    $criteria = HasCriteria::make(new GlobalScope(), $filterParam, fake()->word());

    $this->assertEquals($operator, $criteria->getLogicalOperator());
});
