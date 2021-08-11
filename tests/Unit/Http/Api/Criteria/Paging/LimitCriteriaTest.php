<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Criteria\Paging;

use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\LimitCriteria;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class LimitCriteriaTest.
 */
class LimitCriteriaTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Limit Criteria shall return the provided limit.
     *
     * @return void
     */
    public function testDefaultLimit()
    {
        $limit = $this->faker->numberBetween(1, Criteria::MAX_RESULTS);

        $criteria = new LimitCriteria($limit);

        static::assertEquals($limit, $criteria->getResultSize());
    }

    /**
     * If the limit is greater than the default, the Limit Criteria shall return the default.
     *
     * @return void
     */
    public function testUpperBoundLimit()
    {
        $limit = Criteria::MAX_RESULTS + $this->faker->randomDigitNotNull();

        $criteria = new LimitCriteria($limit);

        static::assertEquals(Criteria::DEFAULT_SIZE, $criteria->getResultSize());
    }

    /**
     * If the limit is lte to zero, the Limit Criteria shall return the default limit.
     *
     * @return void
     */
    public function testLowerBoundLimit()
    {
        $limit = $this->faker->randomDigit() * -1;

        $criteria = new LimitCriteria($limit);

        static::assertEquals(Criteria::DEFAULT_SIZE, $criteria->getResultSize());
    }
}
