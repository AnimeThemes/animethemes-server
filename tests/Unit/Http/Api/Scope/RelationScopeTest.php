<?php

declare(strict_types=1);

use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\RelationScope;
use App\Http\Api\Scope\TypeScope;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('global scope is not within scope', function () {
    $scope = new RelationScope(fake()->word());

    $otherScope = new GlobalScope();

    $this->assertFalse($scope->isWithinScope($otherScope));
});

test('type scope is not within scope', function () {
    $scope = new RelationScope(fake()->word());

    $otherScope = new TypeScope(fake()->word());

    $this->assertFalse($scope->isWithinScope($otherScope));
});

test('unequal relation is not within scope', function () {
    $scope = new RelationScope(fake()->word());

    $otherScope = new RelationScope(fake()->word());

    $this->assertFalse($scope->isWithinScope($otherScope));
});

test('relation is within scope', function () {
    $relation = fake()->word();

    $scope = new RelationScope($relation);

    $otherScope = new RelationScope($relation);

    $this->assertTrue($scope->isWithinScope($otherScope));
});
