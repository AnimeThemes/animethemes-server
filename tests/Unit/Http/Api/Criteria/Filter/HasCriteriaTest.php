<?php

declare(strict_types=1);

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Criteria\Filter\Criteria;
use App\Http\Api\Criteria\Filter\HasCriteria;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('field', function () {
    $criteria = HasCriteria::make(new GlobalScope(), HasCriteria::PARAM_VALUE, fake()->word());

    $this->assertEquals(HasCriteria::PARAM_VALUE, $criteria->getField());
});

test('default comparison operator', function () {
    $criteria = HasCriteria::make(new GlobalScope(), HasCriteria::PARAM_VALUE, fake()->word());

    $this->assertEquals(ComparisonOperator::GTE, $criteria->getComparisonOperator());
});

test('comparison operator', function () {
    $operator = Arr::random(ComparisonOperator::cases());

    $filterParam = Str::of(HasCriteria::PARAM_VALUE)->append(Criteria::PARAM_SEPARATOR)->append($operator->name)->__toString();

    $criteria = HasCriteria::make(new GlobalScope(), $filterParam, fake()->word());

    $this->assertEquals($operator, $criteria->getComparisonOperator());
});

test('default count', function () {
    $criteria = HasCriteria::make(new GlobalScope(), HasCriteria::PARAM_VALUE, fake()->word());

    $this->assertEquals(1, $criteria->getCount());
});

test('count', function () {
    $count = fake()->randomDigitNotNull();

    $filterParam = Str::of(HasCriteria::PARAM_VALUE)->append(Criteria::PARAM_SEPARATOR)->append(strval($count))->__toString();

    $criteria = HasCriteria::make(new GlobalScope(), $filterParam, fake()->word());

    $this->assertEquals($count, $criteria->getCount());
});

test('default logical operator', function () {
    $criteria = HasCriteria::make(new GlobalScope(), fake()->word(), fake()->word());

    $this->assertEquals(BinaryLogicalOperator::AND, $criteria->getLogicalOperator());
});

test('logical operator', function () {
    $operator = Arr::random(BinaryLogicalOperator::cases());

    $filterParam = Str::of(HasCriteria::PARAM_VALUE)->append(Criteria::PARAM_SEPARATOR)->append($operator->name)->__toString();

    $criteria = HasCriteria::make(new GlobalScope(), $filterParam, fake()->word());

    $this->assertEquals($operator, $criteria->getLogicalOperator());
});
