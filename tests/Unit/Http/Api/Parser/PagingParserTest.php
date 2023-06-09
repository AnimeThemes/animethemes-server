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

/**
 * Class PagingParserTest.
 */
class PagingParserTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Paging Parser shall return Limit Criteria.
     *
     * @return void
     */
    public function testParseLimitCriteriaByDefault(): void
    {
        $parameters = [];

        $criteria = collect(PagingParser::parse($parameters))->first(function (Criteria $criteria) {
            return PaginationStrategy::LIMIT === $criteria->getStrategy();
        });

        static::assertInstanceOf(LimitCriteria::class, $criteria);
    }

    /**
     * If the provided limit is invalid, the Limit Parser shall use the default.
     *
     * @return void
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
            return PaginationStrategy::LIMIT === $criteria->getStrategy();
        });

        static::assertTrue(
            $criteria instanceof LimitCriteria
            && $criteria->getResultSize() === Criteria::DEFAULT_SIZE
        );
    }

    /**
     * The Paging Parser shall parse valid limits.
     *
     * @return void
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
            return PaginationStrategy::LIMIT === $criteria->getStrategy();
        });

        static::assertTrue(
            $criteria instanceof LimitCriteria
            && $criteria->getResultSize() === $limit
        );
    }

    /**
     * By default, the Paging Parser shall return Offset Criteria.
     *
     * @return void
     */
    public function testParseOffsetCriteriaByDefault(): void
    {
        $parameters = [];

        $criteria = collect(PagingParser::parse($parameters))->first(function (Criteria $criteria) {
            return PaginationStrategy::OFFSET === $criteria->getStrategy();
        });

        static::assertInstanceOf(OffsetCriteria::class, $criteria);
    }

    /**
     * If the provided size is invalid, the Limit Parser shall use the default.
     *
     * @return void
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
            return PaginationStrategy::OFFSET === $criteria->getStrategy();
        });

        static::assertTrue(
            $criteria instanceof OffsetCriteria
            && $criteria->getResultSize() === Criteria::DEFAULT_SIZE
        );
    }

    /**
     * The Paging Parser shall parse valid sizes.
     *
     * @return void
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
            return PaginationStrategy::OFFSET === $criteria->getStrategy();
        });

        static::assertTrue(
            $criteria instanceof OffsetCriteria
            && $criteria->getResultSize() === $size
        );
    }
}
