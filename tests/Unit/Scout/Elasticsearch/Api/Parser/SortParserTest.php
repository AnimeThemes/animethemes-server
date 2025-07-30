<?php

declare(strict_types=1);

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Sort\FieldCriteria as BaseFieldCriteria;
use App\Http\Api\Criteria\Sort\RandomCriteria;
use App\Http\Api\Criteria\Sort\RelationCriteria as BaseRelationCriteria;
use App\Http\Api\Scope\GlobalScope;
use App\Scout\Elasticsearch\Api\Criteria\Sort\FieldCriteria;
use App\Scout\Elasticsearch\Api\Criteria\Sort\RelationCriteria;
use App\Scout\Elasticsearch\Api\Parser\SortParser;
use Illuminate\Support\Arr;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('relation criteria', function () {
    $direction = Arr::random(Direction::cases());

    $criteria = new BaseRelationCriteria(new GlobalScope(), fake()->word(), $direction);

    static::assertInstanceOf(RelationCriteria::class, SortParser::parse($criteria));
});

test('field criteria', function () {
    $direction = Arr::random(Direction::cases());

    $criteria = new BaseFieldCriteria(new GlobalScope(), fake()->word(), $direction);

    static::assertInstanceOf(FieldCriteria::class, SortParser::parse($criteria));
});

test('random criteria', function () {
    $criteria = new RandomCriteria(new GlobalScope());

    static::assertNull(SortParser::parse($criteria));
});
