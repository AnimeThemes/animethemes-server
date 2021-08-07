<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Filter;

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
    public function testParseLimitCriteriaByDefault()
    {
        $parameters = [];

        $criteria = collect(PagingParser::parse($parameters))->first(function (Criteria $criteria) {
            return $criteria->getStrategy()->is(PaginationStrategy::LIMIT());
        });

        static::assertInstanceOf(LimitCriteria::class, $criteria);
    }

    /**
     * If the provided limit is invalid, the Limit Parser shall use the default.
     *
     * @return void
     */
    public function testParseInvalidLimitCriteria()
    {
        $limit = $this->faker->word();

        $parameters = [
            PagingParser::$param => [
                LimitCriteria::PARAM => $limit,
            ]
        ];

        $criteria = collect(PagingParser::parse($parameters))->first(function (Criteria $criteria) {
            return $criteria->getStrategy()->is(PaginationStrategy::LIMIT());
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
    public function testParseValidLimitCriteria()
    {
        $limit = $this->faker->numberBetween(1, Criteria::DEFAULT_SIZE);

        $parameters = [
            PagingParser::$param => [
                LimitCriteria::PARAM => $limit,
            ]
        ];

        $criteria = collect(PagingParser::parse($parameters))->first(function (Criteria $criteria) {
            return $criteria->getStrategy()->is(PaginationStrategy::LIMIT());
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
    public function testParseOffsetCriteriaByDefault()
    {
        $parameters = [];

        $criteria = collect(PagingParser::parse($parameters))->first(function (Criteria $criteria) {
            return $criteria->getStrategy()->is(PaginationStrategy::OFFSET());
        });

        static::assertInstanceOf(OffsetCriteria::class, $criteria);
    }

    /**
     * If the provided size is invalid, the Limit Parser shall use the default.
     *
     * @return void
     */
    public function testParseInvalidOffsetCriteria()
    {
        $size = $this->faker->word();

        $parameters = [
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => $size,
            ]
        ];

        $criteria = collect(PagingParser::parse($parameters))->first(function (Criteria $criteria) {
            return $criteria->getStrategy()->is(PaginationStrategy::OFFSET());
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
    public function testParseValidOffsetCriteria()
    {
        $size = $this->faker->numberBetween(1, Criteria::MAX_RESULTS);

        $parameters = [
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => $size,
            ]
        ];

        $criteria = collect(PagingParser::parse($parameters))->first(function (Criteria $criteria) {
            return $criteria->getStrategy()->is(PaginationStrategy::OFFSET());
        });

        static::assertTrue(
            $criteria instanceof OffsetCriteria
            && $criteria->getResultSize() === $size
        );
    }
}
