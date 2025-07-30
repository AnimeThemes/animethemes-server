<?php

declare(strict_types=1);

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Criteria\Field\Criteria as FieldCriteria;
use App\Http\Api\Criteria\Include\Criteria as IncludeCriteria;
use App\Http\Api\Criteria\Include\ResourceCriteria;
use App\Http\Api\Criteria\Paging\LimitCriteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Criteria\Search\Criteria as SearchCriteria;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\SearchParser;
use App\Http\Api\Parser\SortParser;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Tests\Unit\Http\Api\Query\FakeQuery;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('get field criteria', function () {
    $type = fake()->word();

    $parameters = [
        FieldParser::param() => [
            $type => fake()->word(),
        ],
    ];

    $query = new FakeQuery($parameters);

    static::assertInstanceOf(FieldCriteria::class, $query->getFieldCriteria($type));
});

test('get include criteria', function () {
    $parameters = [
        IncludeParser::param() => fake()->word(),
    ];

    $query = new FakeQuery($parameters);

    static::assertInstanceOf(IncludeCriteria::class, $query->getIncludeCriteria(fake()->word()));
});

test('get include resource criteria', function () {
    $type = fake()->word();

    $parameters = [
        IncludeParser::param() => [
            $type => fake()->word(),
        ],
    ];

    $query = new FakeQuery($parameters);

    static::assertInstanceOf(ResourceCriteria::class, $query->getIncludeCriteria($type));
});

test('get sort criteria', function () {
    $fields = collect(fake()->words(fake()->randomDigitNotNull()));

    $parameters = [
        SortParser::param() => $fields->join(','),
    ];

    $query = new FakeQuery($parameters);

    static::assertCount($fields->count(), $query->getSortCriteria());
});

test('get filter criteria', function () {
    $filterCount = fake()->randomDigitNotNull();

    $parameters = Collection::times($filterCount, fn () => FilterParser::param().'.'.Str::random())
        ->combine(Collection::times($filterCount, fn () => Str::random()))
        ->undot()
        ->all();

    $query = new FakeQuery($parameters);

    static::assertCount($filterCount, $query->getFilterCriteria());
});

test('does not have search', function () {
    $parameters = [];

    $query = new FakeQuery($parameters);

    static::assertFalse($query->hasSearchCriteria());
});

test('has search', function () {
    $parameters = [
        SearchParser::param() => fake()->word(),
    ];

    $query = new FakeQuery($parameters);

    static::assertTrue($query->hasSearchCriteria());
});

test('null search', function () {
    $parameters = [];

    $query = new FakeQuery($parameters);

    static::assertNull($query->getSearchCriteria());
});

test('get search', function () {
    $parameters = [
        SearchParser::param() => fake()->word(),
    ];

    $query = new FakeQuery($parameters);

    static::assertInstanceOf(SearchCriteria::class, $query->getSearchCriteria());
});

test('get limit criteria', function () {
    $parameters = [];

    $query = new FakeQuery($parameters);

    static::assertInstanceOf(LimitCriteria::class, $query->getPagingCriteria(PaginationStrategy::LIMIT));
});

test('get offset criteria', function () {
    $parameters = [];

    $query = new FakeQuery($parameters);

    static::assertInstanceOf(OffsetCriteria::class, $query->getPagingCriteria(PaginationStrategy::OFFSET));
});
