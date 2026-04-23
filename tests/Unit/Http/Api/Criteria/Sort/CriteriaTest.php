<?php

declare(strict_types=1);

use App\Http\Api\Criteria\Sort\Criteria;
use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\TypeScope;
use App\Http\Api\Sort\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

uses(WithFaker::class);

test('should not sort if key mismatch', function (): void {
    $criteria = new class(new GlobalScope(), fake()->unique()->word()) extends Criteria
    {
        /**
         * Apply criteria to builder.
         */
        public function sort(Builder $builder, Sort $sort): Builder
        {
            return $builder;
        }
    };

    $sort = new Sort(fake()->unique()->word());

    $this->assertFalse($criteria->shouldSort($sort, $criteria->getScope()));
});

test('should sort if key match', function (): void {
    $key = fake()->word();

    $criteria = new class(new GlobalScope(), $key) extends Criteria
    {
        /**
         * Apply criteria to builder.
         */
        public function sort(Builder $builder, Sort $sort): Builder
        {
            return $builder;
        }
    };

    $sort = new Sort($key);

    $this->assertTrue($criteria->shouldSort($sort, $criteria->getScope()));
});

test('should not sort if not within scope', function (): void {
    $key = fake()->word();

    $scope = new TypeScope(fake()->word());

    $criteria = new class($scope, $key) extends Criteria
    {
        /**
         * Apply criteria to builder.
         */
        public function sort(Builder $builder, Sort $sort): Builder
        {
            return $builder;
        }
    };

    $sort = new Sort($key);

    $this->assertFalse($criteria->shouldSort($sort, new GlobalScope()));
});

test('should sort if within scope', function (): void {
    $key = fake()->word();

    $scope = new TypeScope(Str::of(Str::random())->lower()->singular()->__toString());

    $criteria = new class($scope, $key) extends Criteria
    {
        /**
         * Apply criteria to builder.
         */
        public function sort(Builder $builder, Sort $sort): Builder
        {
            return $builder;
        }
    };

    $sort = new Sort($key);

    $this->assertTrue($criteria->shouldSort($sort, $scope));
});
