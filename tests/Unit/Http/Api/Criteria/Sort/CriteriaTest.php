<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Criteria\Sort;

use App\Http\Api\Criteria\Sort\Criteria;
use App\Http\Api\Sort\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class CriteriaTest.
 */
class CriteriaTest extends TestCase
{
    use WithFaker;

    /**
     * If the criteria and sort keys do not match, the sort should not be applied.
     *
     * @return void
     */
    public function testShouldNotSortIfKeyMismatch(): void
    {
        $criteria = new class($this->faker->word()) extends Criteria
        {
            /**
             * Apply criteria to builder.
             *
             * @param  Builder  $builder
             * @param  Sort  $sort
             * @return Builder
             */
            public function sort(Builder $builder, Sort $sort): Builder
            {
                return $builder;
            }
        };

        $sort = new Sort($this->faker->word());

        static::assertFalse($criteria->shouldSort($sort));
    }

    /**
     * If the criteria and sort keys match, the sort should be applied.
     *
     * @return void
     */
    public function testShouldSortIfKeyMatch(): void
    {
        $key = $this->faker->word();

        $criteria = new class($key) extends Criteria
        {
            /**
             * Apply criteria to builder.
             *
             * @param  Builder  $builder
             * @param  Sort  $sort
             * @return Builder
             */
            public function sort(Builder $builder, Sort $sort): Builder
            {
                return $builder;
            }
        };

        $sort = new Sort($key);

        static::assertTrue($criteria->shouldSort($sort));
    }
}
