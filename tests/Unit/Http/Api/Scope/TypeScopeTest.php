<?php

declare(strict_types=1);

use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\RelationScope;
use App\Http\Api\Scope\TypeScope;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('global scope is not within scope', function (): void {
    $scope = new TypeScope(fake()->word());

    $otherScope = new GlobalScope();

    expect($scope->isWithinScope($otherScope))->toBeFalse();
});

test('type scope is not within scope', function (): void {
    $scope = new TypeScope(fake()->unique()->word());

    $otherScope = new TypeScope(fake()->unique()->word());

    expect($scope->isWithinScope($otherScope))->toBeFalse();
});

test('type scope is within scope', function (): void {
    $type = fake()->word();

    $scope = new TypeScope($type);

    $otherScope = new TypeScope($type);

    expect($scope->isWithinScope($otherScope))->toBeTrue();
});

test('relation scope is not within scope', function (): void {
    $scope = new TypeScope(fake()->word());

    $otherScope = new RelationScope(fake()->word());

    expect($scope->isWithinScope($otherScope))->toBeFalse();
});

test('relation scope is within scope', function (): void {
    $type = fake()->word();

    $scope = new TypeScope($type);

    $otherScope = new RelationScope($type);

    expect($scope->isWithinScope($otherScope))->toBeTrue();
});
