<?php

declare(strict_types=1);

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Criteria\Paging\Criteria;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;

use function Pest\Laravel\get;

uses(WithFaker::class);

test('default size', function (): void {
    $resultSize = fake()->numberBetween(1, Criteria::MAX_RESULTS);

    $criteria = new class($resultSize) extends Criteria
    {
        /**
         * Get the intended pagination strategy.
         */
        public function getStrategy(): PaginationStrategy
        {
            return Arr::random(PaginationStrategy::cases());
        }

        /**
         * Paginate the query.
         */
        public function paginate(Builder $builder): Paginator
        {
            return $builder->paginate();
        }
    };

    $this->assertEquals($resultSize, $criteria->getResultSize());
});

test('upper bound size', function (): void {
    $resultSize = Criteria::MAX_RESULTS + fake()->randomDigitNotNull();

    $criteria = new class($resultSize) extends Criteria
    {
        /**
         * Get the intended pagination strategy.
         */
        public function getStrategy(): PaginationStrategy
        {
            return Arr::random(PaginationStrategy::cases());
        }

        /**
         * Paginate the query.
         */
        public function paginate(Builder $builder): Paginator
        {
            return $builder->paginate();
        }
    };

    $this->assertEquals(Criteria::DEFAULT_SIZE, $criteria->getResultSize());
});

test('lower bound size', function (): void {
    $resultSize = fake()->randomDigit() * -1;

    $criteria = new class($resultSize) extends Criteria
    {
        /**
         * Get the intended pagination strategy.
         */
        public function getStrategy(): PaginationStrategy
        {
            return Arr::random(PaginationStrategy::cases());
        }

        /**
         * Paginate the query.
         */
        public function paginate(Builder $builder): Paginator
        {
            return $builder->paginate();
        }
    };

    $this->assertEquals(Criteria::DEFAULT_SIZE, $criteria->getResultSize());
});
