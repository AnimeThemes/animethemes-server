<?php

declare(strict_types=1);

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\LimitCriteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Parser\PagingParser;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('parse limit criteria by default', function (): void {
    $parameters = [];

    $criteria = collect(PagingParser::parse($parameters))->first(fn (Criteria $criteria): bool => $criteria->getStrategy() === PaginationStrategy::LIMIT);

    expect($criteria)->toBeInstanceOf(LimitCriteria::class);
});

test('parse invalid limit criteria', function (): void {
    $limit = fake()->word();

    $parameters = [
        PagingParser::param() => [
            LimitCriteria::PARAM => $limit,
        ],
    ];

    $criteria = collect(PagingParser::parse($parameters))->first(fn (Criteria $criteria): bool => $criteria->getStrategy() === PaginationStrategy::LIMIT);

    expect($criteria instanceof LimitCriteria
    && $criteria->getResultSize() === Criteria::DEFAULT_SIZE)->toBeTrue();
});

test('parse valid limit criteria', function (): void {
    $limit = fake()->numberBetween(1, Criteria::DEFAULT_SIZE);

    $parameters = [
        PagingParser::param() => [
            LimitCriteria::PARAM => $limit,
        ],
    ];

    $criteria = collect(PagingParser::parse($parameters))->first(fn (Criteria $criteria): bool => $criteria->getStrategy() === PaginationStrategy::LIMIT);

    expect($criteria instanceof LimitCriteria
    && $criteria->getResultSize() === $limit)->toBeTrue();
});

test('parse offset criteria by default', function (): void {
    $parameters = [];

    $criteria = collect(PagingParser::parse($parameters))->first(fn (Criteria $criteria): bool => $criteria->getStrategy() === PaginationStrategy::OFFSET);

    expect($criteria)->toBeInstanceOf(OffsetCriteria::class);
});

test('parse invalid offset criteria', function (): void {
    $size = fake()->word();

    $parameters = [
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => $size,
        ],
    ];

    $criteria = collect(PagingParser::parse($parameters))->first(fn (Criteria $criteria): bool => $criteria->getStrategy() === PaginationStrategy::OFFSET);

    expect($criteria instanceof OffsetCriteria
    && $criteria->getResultSize() === Criteria::DEFAULT_SIZE)->toBeTrue();
});

test('parse valid offset criteria', function (): void {
    $size = fake()->numberBetween(1, Criteria::MAX_RESULTS);

    $parameters = [
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => $size,
        ],
    ];

    $criteria = collect(PagingParser::parse($parameters))->first(fn (Criteria $criteria): bool => $criteria->getStrategy() === PaginationStrategy::OFFSET);

    expect($criteria instanceof OffsetCriteria
    && $criteria->getResultSize() === $size)->toBeTrue();
});
