<?php

declare(strict_types=1);

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\LimitCriteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Parser\PagingParser;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('parse limit criteria by default', function () {
    $parameters = [];

    $criteria = collect(PagingParser::parse($parameters))->first(function (Criteria $criteria) {
        return $criteria->getStrategy() === PaginationStrategy::LIMIT;
    });

    static::assertInstanceOf(LimitCriteria::class, $criteria);
});

test('parse invalid limit criteria', function () {
    $limit = fake()->word();

    $parameters = [
        PagingParser::param() => [
            LimitCriteria::PARAM => $limit,
        ],
    ];

    $criteria = collect(PagingParser::parse($parameters))->first(function (Criteria $criteria) {
        return $criteria->getStrategy() === PaginationStrategy::LIMIT;
    });

    static::assertTrue(
        $criteria instanceof LimitCriteria
        && $criteria->getResultSize() === Criteria::DEFAULT_SIZE
    );
});

test('parse valid limit criteria', function () {
    $limit = fake()->numberBetween(1, Criteria::DEFAULT_SIZE);

    $parameters = [
        PagingParser::param() => [
            LimitCriteria::PARAM => $limit,
        ],
    ];

    $criteria = collect(PagingParser::parse($parameters))->first(function (Criteria $criteria) {
        return $criteria->getStrategy() === PaginationStrategy::LIMIT;
    });

    static::assertTrue(
        $criteria instanceof LimitCriteria
        && $criteria->getResultSize() === $limit
    );
});

test('parse offset criteria by default', function () {
    $parameters = [];

    $criteria = collect(PagingParser::parse($parameters))->first(function (Criteria $criteria) {
        return $criteria->getStrategy() === PaginationStrategy::OFFSET;
    });

    static::assertInstanceOf(OffsetCriteria::class, $criteria);
});

test('parse invalid offset criteria', function () {
    $size = fake()->word();

    $parameters = [
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => $size,
        ],
    ];

    $criteria = collect(PagingParser::parse($parameters))->first(function (Criteria $criteria) {
        return $criteria->getStrategy() === PaginationStrategy::OFFSET;
    });

    static::assertTrue(
        $criteria instanceof OffsetCriteria
        && $criteria->getResultSize() === Criteria::DEFAULT_SIZE
    );
});

test('parse valid offset criteria', function () {
    $size = fake()->numberBetween(1, Criteria::MAX_RESULTS);

    $parameters = [
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => $size,
        ],
    ];

    $criteria = collect(PagingParser::parse($parameters))->first(function (Criteria $criteria) {
        return $criteria->getStrategy() === PaginationStrategy::OFFSET;
    });

    static::assertTrue(
        $criteria instanceof OffsetCriteria
        && $criteria->getResultSize() === $size
    );
});
