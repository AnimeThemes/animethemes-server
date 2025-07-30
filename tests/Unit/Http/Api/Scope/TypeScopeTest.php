<?php

declare(strict_types=1);

use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\RelationScope;
use App\Http\Api\Scope\TypeScope;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('global scope is not within scope', function () {
    $scope = new TypeScope(fake()->word());

    $otherScope = new GlobalScope();

    $this->assertFalse($scope->isWithinScope($otherScope));
});

test('type scope is not within scope', function () {
    $scope = new TypeScope(fake()->word());

    $otherScope = new TypeScope(fake()->word());

    $this->assertFalse($scope->isWithinScope($otherScope));
});

test('type scope is within scope', function () {
    $type = fake()->word();

    $scope = new TypeScope($type);

    $otherScope = new TypeScope($type);

    $this->assertTrue($scope->isWithinScope($otherScope));
});

test('relation scope is not within scope', function () {
    $scope = new TypeScope(fake()->word());

    $otherScope = new RelationScope(fake()->word());

    $this->assertFalse($scope->isWithinScope($otherScope));
});

test('relation scope is within scope', function () {
    $type = fake()->word();

    $scope = new TypeScope($type);

    $otherScope = new RelationScope($type);

    $this->assertTrue($scope->isWithinScope($otherScope));
});
