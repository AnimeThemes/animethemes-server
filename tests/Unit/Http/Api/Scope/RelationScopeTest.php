<?php

declare(strict_types=1);

use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\RelationScope;
use App\Http\Api\Scope\TypeScope;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('global scope is not within scope', function (): void {
    $scope = new RelationScope(fake()->word());

    $otherScope = new GlobalScope();

    $this->assertFalse($scope->isWithinScope($otherScope));
});

test('type scope is not within scope', function (): void {
    $scope = new RelationScope(fake()->word());

    $otherScope = new TypeScope(fake()->word());

    $this->assertFalse($scope->isWithinScope($otherScope));
});

test('unequal relation is not within scope', function (): void {
    $scope = new RelationScope(fake()->unique()->word());

    $otherScope = new RelationScope(fake()->unique()->word());

    $this->assertFalse($scope->isWithinScope($otherScope));
});

test('relation is within scope', function (): void {
    $relation = fake()->word();

    $scope = new RelationScope($relation);

    $otherScope = new RelationScope($relation);

    $this->assertTrue($scope->isWithinScope($otherScope));
});
