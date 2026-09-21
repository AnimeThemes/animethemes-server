<?php

declare(strict_types=1);

use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\RelationScope;
use App\Http\Api\Scope\TypeScope;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('global scope is within scope', function (): void {
    $scope = new GlobalScope();

    $otherScope = new GlobalScope();

    expect($scope->isWithinScope($otherScope))->toBeTrue();
});

test('type scope is within scope', function (): void {
    $scope = new GlobalScope();

    $otherScope = new TypeScope(fake()->word());

    expect($scope->isWithinScope($otherScope))->toBeTrue();
});

test('relation scope is within scope', function (): void {
    $scope = new GlobalScope();

    $otherScope = new RelationScope(fake()->word());

    expect($scope->isWithinScope($otherScope))->toBeTrue();
});
