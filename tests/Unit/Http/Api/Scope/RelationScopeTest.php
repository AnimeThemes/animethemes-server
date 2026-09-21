<?php

declare(strict_types=1);

use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\RelationScope;
use App\Http\Api\Scope\TypeScope;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('global scope is not within scope', function (): void {
    $scope = new RelationScope(fake()->word());

    $otherScope = new GlobalScope();

    expect($scope->isWithinScope($otherScope))->toBeFalse();
});

test('type scope is not within scope', function (): void {
    $scope = new RelationScope(fake()->word());

    $otherScope = new TypeScope(fake()->word());

    expect($scope->isWithinScope($otherScope))->toBeFalse();
});

test('unequal relation is not within scope', function (): void {
    $scope = new RelationScope(fake()->unique()->word());

    $otherScope = new RelationScope(fake()->unique()->word());

    expect($scope->isWithinScope($otherScope))->toBeFalse();
});

test('relation is within scope', function (): void {
    $relation = fake()->word();

    $scope = new RelationScope($relation);

    $otherScope = new RelationScope($relation);

    expect($scope->isWithinScope($otherScope))->toBeTrue();
});
