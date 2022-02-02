<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Elasticsearch\Api\Parser;

use App\Http\Api\Criteria\Paging\LimitCriteria as BaseLimitCriteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria as BaseOffsetCriteria;
use App\Services\Elasticsearch\Api\Criteria\Paging\LimitCriteria;
use App\Services\Elasticsearch\Api\Criteria\Paging\OffsetCriteria;
use App\Services\Elasticsearch\Api\Parser\PagingParser;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class PagingParserTest.
 */
class PagingParserTest extends TestCase
{
    use WithFaker;

    /**
     * The Paging Parser shall parse Limit Criteria.
     *
     * @return void
     */
    public function testLimitCriteria()
    {
        $criteria = new BaseLimitCriteria($this->faker->randomDigitNotNull());

        static::assertInstanceOf(LimitCriteria::class, PagingParser::parse($criteria));
    }

    /**
     * The Paging Parser shall parse Offset Criteria.
     *
     * @return void
     */
    public function testOffsetCriteria()
    {
        $criteria = new BaseOffsetCriteria($this->faker->randomDigitNotNull());

        static::assertInstanceOf(OffsetCriteria::class, PagingParser::parse($criteria));
    }
}
