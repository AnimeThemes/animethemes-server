<?php

declare(strict_types=1);

use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\RelationScope;
use App\Http\Api\Scope\ScopeParser;
use App\Http\Api\Scope\TypeScope;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('parse global scope', function (): void {
    expect(ScopeParser::parse(''))->toBeInstanceOf(GlobalScope::class);
});

test('parse type scope', function (): void {
    expect(ScopeParser::parse(fake()->word()))->toBeInstanceOf(TypeScope::class);
});

test('parse relation scope', function (): void {
    $relation = collect(fake()->words())->join('.');

    expect(ScopeParser::parse($relation))->toBeInstanceOf(RelationScope::class);
});
