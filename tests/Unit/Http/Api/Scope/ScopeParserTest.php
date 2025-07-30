<?php

declare(strict_types=1);

use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\RelationScope;
use App\Http\Api\Scope\ScopeParser;
use App\Http\Api\Scope\TypeScope;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('parse global scope', function () {
    $this->assertInstanceOf(GlobalScope::class, ScopeParser::parse(''));
});

test('parse type scope', function () {
    $this->assertInstanceOf(TypeScope::class, ScopeParser::parse(fake()->word()));
});

test('parse relation scope', function () {
    $relation = collect(fake()->words())->join('.');

    $this->assertInstanceOf(RelationScope::class, ScopeParser::parse($relation));
});
