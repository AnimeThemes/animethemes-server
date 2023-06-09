<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Criteria\Paging;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Criteria\Paging\Criteria;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class CriteriaTest.
 */
class CriteriaTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Paging Criteria shall return the provided size.
     *
     * @return void
     */
    public function testDefaultSize(): void
    {
        $resultSize = $this->faker->numberBetween(1, Criteria::MAX_RESULTS);

        $criteria = new class($resultSize) extends Criteria
        {
            /**
             * Get the intended pagination strategy.
             *
             * @return PaginationStrategy
             */
            public function getStrategy(): PaginationStrategy
            {
                return Arr::random(PaginationStrategy::cases());
            }

            /**
             * Paginate the query.
             *
             * @param  Builder  $builder
             * @return Collection|Paginator
             */
            public function paginate(Builder $builder): Collection|Paginator
            {
                return $builder->paginate();
            }
        };

        static::assertEquals($resultSize, $criteria->getResultSize());
    }

    /**
     * If the size is greater than the default, the paging Criteria shall return the default.
     *
     * @return void
     */
    public function testUpperBoundSize(): void
    {
        $resultSize = Criteria::MAX_RESULTS + $this->faker->randomDigitNotNull();

        $criteria = new class($resultSize) extends Criteria
        {
            /**
             * Get the intended pagination strategy.
             *
             * @return PaginationStrategy
             */
            public function getStrategy(): PaginationStrategy
            {
                return Arr::random(PaginationStrategy::cases());
            }

            /**
             * Paginate the query.
             *
             * @param  Builder  $builder
             * @return Collection|Paginator
             */
            public function paginate(Builder $builder): Collection|Paginator
            {
                return $builder->paginate();
            }
        };

        static::assertEquals(Criteria::DEFAULT_SIZE, $criteria->getResultSize());
    }

    /**
     * If the size is lte to zero, the paging Criteria shall return the default size.
     *
     * @return void
     */
    public function testLowerBoundSize(): void
    {
        $resultSize = $this->faker->randomDigit() * -1;

        $criteria = new class($resultSize) extends Criteria
        {
            /**
             * Get the intended pagination strategy.
             *
             * @return PaginationStrategy
             */
            public function getStrategy(): PaginationStrategy
            {
                return Arr::random(PaginationStrategy::cases());
            }

            /**
             * Paginate the query.
             *
             * @param  Builder  $builder
             * @return Collection|Paginator
             */
            public function paginate(Builder $builder): Collection|Paginator
            {
                return $builder->paginate();
            }
        };

        static::assertEquals(Criteria::DEFAULT_SIZE, $criteria->getResultSize());
    }
}
