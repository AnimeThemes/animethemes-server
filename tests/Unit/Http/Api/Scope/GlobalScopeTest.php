<?php

declare(strict_types=1);

use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\RelationScope;
use App\Http\Api\Scope\TypeScope;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('global scope is within scope', function () {
    $scope = new GlobalScope();

    $otherScope = new GlobalScope();

    $this->assertTrue($scope->isWithinScope($otherScope));
});

test('type scope is within scope', function () {
    $scope = new GlobalScope();

    $otherScope = new TypeScope(fake()->word());

    $this->assertTrue($scope->isWithinScope($otherScope));
});

test('relation scope is within scope', function () {
    $scope = new GlobalScope();

    $otherScope = new RelationScope(fake()->word());

    $this->assertTrue($scope->isWithinScope($otherScope));
});
