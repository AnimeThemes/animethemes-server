<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Criteria\Paging;

use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class OffsetCriteriaTest.
 */
class OffsetCriteriaTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Offset Criteria shall return the provided size.
     *
     * @return void
     */
    public function testDefaultSize()
    {
        $size = $this->faker->numberBetween(1, Criteria::MAX_RESULTS);

        $criteria = new OffsetCriteria($size);

        static::assertEquals($size, $criteria->getResultSize());
    }

    /**
     * If the size is greater than the max, the Offset Criteria shall return the default size.
     *
     * @return void
     */
    public function testUpperBoundLimit()
    {
        $size = Criteria::MAX_RESULTS + $this->faker->randomDigitNotNull();

        $criteria = new OffsetCriteria($size);

        static::assertEquals(Criteria::DEFAULT_SIZE, $criteria->getResultSize());
    }

    /**
     * If the size is lte to zero, the Offset Criteria shall return the default size.
     *
     * @return void
     */
    public function testLowerBoundLimit()
    {
        $size = $this->faker->randomDigit() * -1;

        $criteria = new OffsetCriteria($size);

        static::assertEquals(Criteria::DEFAULT_SIZE, $criteria->getResultSize());
    }
}
