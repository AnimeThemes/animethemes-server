<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Sort;

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Sort\FieldCriteria;
use App\Http\Api\Criteria\Sort\RandomCriteria;
use App\Http\Api\Sort\RandomSort;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class RandomSortTest.
 */
class RandomSortTest extends TestCase
{
    use WithFaker;

    /**
     * A Random sort shall not be applied if there exist other sort criteria.
     *
     * @return void
     */
    public function testShouldNotApplySort()
    {
        $criteria = Collection::make();

        Collection::times($this->faker->numberBetween(2, 9), function () use (&$criteria) {
            $criteria->push(new FieldCriteria($this->faker->word(), Direction::getRandomInstance()));
        });

        $criteria->push(new RandomCriteria());

        $sort = new RandomSort($criteria);

        static::assertFalse($sort->shouldApplySort());
    }

    /**
     * A Random sort shall be applied if there exist other sort criteria.
     *
     * @return void
     */
    public function testShouldApplySort()
    {
        $criteria = Collection::make();

        $criteria->push(new RandomCriteria());

        $sort = new RandomSort($criteria);

        static::assertTrue($sort->shouldApplySort());
    }
}
