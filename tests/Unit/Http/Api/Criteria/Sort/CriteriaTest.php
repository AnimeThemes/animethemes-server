<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Criteria\Sort;

use App\Http\Api\Criteria\Sort\Criteria;
use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\TypeScope;
use App\Http\Api\Sort\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
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
        $criteria = new class(new GlobalScope(), $this->faker->word()) extends Criteria
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

        static::assertFalse($criteria->shouldSort($sort, $criteria->getScope()));
    }

    /**
     * If the criteria and sort keys match, the sort should be applied.
     *
     * @return void
     */
    public function testShouldSortIfKeyMatch(): void
    {
        $key = $this->faker->word();

        $criteria = new class(new GlobalScope(), $key) extends Criteria
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

        static::assertTrue($criteria->shouldSort($sort, $criteria->getScope()));
    }

    /**
     * If the criteria and sort keys match, the sort should be applied.
     *
     * @return void
     */
    public function testShouldNotSortIfNotWithinScope(): void
    {
        $key = $this->faker->word();

        $scope = new TypeScope($this->faker->word());

        $criteria = new class($scope, $key) extends Criteria
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

        static::assertFalse($criteria->shouldSort($sort, new GlobalScope()));
    }

    /**
     * If the criteria and filter keys match, the filter should be applied.
     *
     * @return void
     */
    public function testShouldFilterIfWithinScope(): void
    {
        $key = $this->faker->word();

        $scope = new TypeScope(Str::of(Str::random())->lower()->singular()->__toString());

        $criteria = new class($scope, $key) extends Criteria
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

        static::assertTrue($criteria->shouldSort($sort, $scope));
    }
}
