<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Parser;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\LimitCriteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Parser\PagingParser;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PagingParserTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Paging Parser shall return Limit Criteria.
     */
    public function testParseLimitCriteriaByDefault(): void
    {
        $parameters = [];

        $criteria = collect(PagingParser::parse($parameters))->first(function (Criteria $criteria) {
            return $criteria->getStrategy() === PaginationStrategy::LIMIT;
        });

        static::assertInstanceOf(LimitCriteria::class, $criteria);
    }

    /**
     * If the provided limit is invalid, the Limit Parser shall use the default.
     */
    public function testParseInvalidLimitCriteria(): void
    {
        $limit = $this->faker->word();

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
    }

    /**
     * The Paging Parser shall parse valid limits.
     */
    public function testParseValidLimitCriteria(): void
    {
        $limit = $this->faker->numberBetween(1, Criteria::DEFAULT_SIZE);

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
    }

    /**
     * By default, the Paging Parser shall return Offset Criteria.
     */
    public function testParseOffsetCriteriaByDefault(): void
    {
        $parameters = [];

        $criteria = collect(PagingParser::parse($parameters))->first(function (Criteria $criteria) {
            return $criteria->getStrategy() === PaginationStrategy::OFFSET;
        });

        static::assertInstanceOf(OffsetCriteria::class, $criteria);
    }

    /**
     * If the provided size is invalid, the Limit Parser shall use the default.
     */
    public function testParseInvalidOffsetCriteria(): void
    {
        $size = $this->faker->word();

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
    }

    /**
     * The Paging Parser shall parse valid sizes.
     */
    public function testParseValidOffsetCriteria(): void
    {
        $size = $this->faker->numberBetween(1, Criteria::MAX_RESULTS);

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
    }
}
