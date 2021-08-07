<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Sort;

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Sort\FieldCriteria;
use App\Http\Api\Query;
use App\Http\Api\Sort\Sort;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
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
        $sortField = $this->faker->word();

        $parameters = [];

        $query = Query::make($parameters);

        $sort = new class($query->getSortCriteria(), $sortField) extends Sort
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

        $criteria = Collection::make();

        Collection::times($this->faker->numberBetween(2, 9), function () use (&$criteria, $sortField) {
            $criteria->push(new FieldCriteria($sortField, Direction::getRandomInstance()));
        });

        $parameters = [];

        $query = Query::make($parameters);

        $sort = new class($query->getSortCriteria(), $sortField) extends Sort
        {
            // We don't need to do any customization
        };

        static::assertFalse($sort->shouldApplySort());
    }

    /**
     * The sort should be applied if there exists more than one criteria for the key.
     *
     * @return void
     */
    public function testShouldApplySort()
    {
        $sortField = $this->faker->word();

        $criteria = Collection::make();

        $criteria->push(new FieldCriteria($sortField, Direction::getRandomInstance()));

        $parameters = [];

        $query = Query::make($parameters);

        $sort = new class($query->getSortCriteria(), $sortField) extends Sort
        {
            // We don't need to do any customization
        };

        static::assertFalse($sort->shouldApplySort());
    }
}
