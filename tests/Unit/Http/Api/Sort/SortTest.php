<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Sort;

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Sort\FieldCriteria;
use App\Http\Api\Sort\Sort;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class SortTest.
 */
class SortTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the sort column shall be derived from the sort key.
     *
     * @return void
     */
    public function testDefaultColumn()
    {
        $sort = new class($this->faker->word()) extends Sort
        {
            // We don't need to do any customization
        };

        static::assertEquals($sort->getKey(), $sort->getColumn());
    }

    /**
     * The sort should not be applied if there exists more than one criteria for the key.
     *
     * @return void
     */
    public function testShouldNotApplySort()
    {
        $sortField = $this->faker->word();

        $sort = new class($sortField) extends Sort
        {
            // We don't need to do any customization
        };

        $criteria = new FieldCriteria($this->faker->word(), Direction::getRandomInstance());

        static::assertFalse($sort->shouldApplySort($criteria));
    }

    /**
     * The sort should be applied if there exists more than one criteria for the key.
     *
     * @return void
     */
    public function testShouldApplySort()
    {
        $sortField = $this->faker->word();

        $sort = new class($sortField) extends Sort
        {
            // We don't need to do any customization
        };

        $criteria = new FieldCriteria($sortField, Direction::getRandomInstance());

        static::assertTrue($sort->shouldApplySort($criteria));
    }
}
