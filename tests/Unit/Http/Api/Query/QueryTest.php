<?php

declare(strict_types=1);

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Criteria\Field\Criteria as FieldCriteria;
use App\Http\Api\Criteria\Include\Criteria as IncludeCriteria;
use App\Http\Api\Criteria\Include\ResourceCriteria;
use App\Http\Api\Criteria\Paging\LimitCriteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\SearchParser;
use App\Http\Api\Parser\SortParser;
use App\Scout\Criteria as SearchCriteria;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Tests\Unit\Http\Api\Query\FakeQuery;

pest()->use(WithFaker::class);

test('get field criteria', function (): void {
    $type = fake()->word();

    $parameters = [
        FieldParser::param() => [
            $type => fake()->word(),
        ],
    ];

    $query = new FakeQuery($parameters);

    expect($query->getFieldCriteria($type))->toBeInstanceOf(FieldCriteria::class);
});

test('get include criteria', function (): void {
    $parameters = [
        IncludeParser::param() => fake()->word(),
    ];

    $query = new FakeQuery($parameters);

    expect($query->getIncludeCriteria(fake()->word()))->toBeInstanceOf(IncludeCriteria::class);
});

test('get include resource criteria', function (): void {
    $type = fake()->word();

    $parameters = [
        IncludeParser::param() => [
            $type => fake()->word(),
        ],
    ];

    $query = new FakeQuery($parameters);

    expect($query->getIncludeCriteria($type))->toBeInstanceOf(ResourceCriteria::class);
});

test('get sort criteria', function (): void {
    $fields = collect(fake()->words(fake()->randomDigitNotNull()));

    $parameters = [
        SortParser::param() => $fields->join(','),
    ];

    $query = new FakeQuery($parameters);

    expect($query->getSortCriteria())->toHaveCount($fields->count());
});

test('get filter criteria', function (): void {
    $filterCount = fake()->randomDigitNotNull();

    $parameters = Collection::times($filterCount, fn (): string => FilterParser::param().'.'.Str::random())
        ->combine(Collection::times($filterCount, fn () => Str::random()))
        ->undot()
        ->all();

    $query = new FakeQuery($parameters);

    expect($query->getFilterCriteria())->toHaveCount($filterCount);
});

test('does not have search', function (): void {
    $parameters = [];

    $query = new FakeQuery($parameters);

    expect($query->hasSearchCriteria())->toBeFalse();
});

test('has search', function (): void {
    $parameters = [
        SearchParser::param() => fake()->word(),
    ];

    $query = new FakeQuery($parameters);

    expect($query->hasSearchCriteria())->toBeTrue();
});

test('null search', function (): void {
    $parameters = [];

    $query = new FakeQuery($parameters);

    expect($query->getSearchCriteria())->toBeNull();
});

test('get search', function (): void {
    $parameters = [
        SearchParser::param() => fake()->word(),
    ];

    $query = new FakeQuery($parameters);

    expect($query->getSearchCriteria())->toBeInstanceOf(SearchCriteria::class);
});

test('get limit criteria', function (): void {
    $parameters = [];

    $query = new FakeQuery($parameters);

    expect($query->getPagingCriteria(PaginationStrategy::LIMIT))->toBeInstanceOf(LimitCriteria::class);
});

test('get offset criteria', function (): void {
    $parameters = [];

    $query = new FakeQuery($parameters);

    expect($query->getPagingCriteria(PaginationStrategy::OFFSET))->toBeInstanceOf(OffsetCriteria::class);
});
