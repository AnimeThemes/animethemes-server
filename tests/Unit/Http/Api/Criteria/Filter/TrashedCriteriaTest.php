<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Criteria\Filter\TrashedCriteria;
use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\TypeScope;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class TrashedCriteriaTest.
 */
class TrashedCriteriaTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Trashed Criteria shall be scoped globally.
     *
     * @return void
     */
    public function testGlobalScope(): void
    {
        $criteria = TrashedCriteria::make(TrashedCriteria::PARAM_VALUE, $this->faker->word());

        static::assertInstanceOf(GlobalScope::class, $criteria->getScope());
    }

    /**
     * The Trashed Criteria shall parse the scope if provided.
     *
     * @return void
     */
    public function testScope(): void
    {
        $type = Str::of(Str::random())->lower()->singular()->__toString();

        $filterParam = Str::of($type)->append('.')->append(TrashedCriteria::PARAM_VALUE)->__toString();

        $criteria = TrashedCriteria::make($filterParam, $this->faker->word());

        $scope = $criteria->getScope();

        static::assertTrue($scope instanceof TypeScope && $scope->getType() === $type);
    }

    /**
     * The Trashed Criteria shall parse the field.
     *
     * @return void
     */
    public function testField(): void
    {
        $criteria = TrashedCriteria::make(TrashedCriteria::PARAM_VALUE, $this->faker->word());

        static::assertEquals(TrashedCriteria::PARAM_VALUE, $criteria->getField());
    }

    /**
     * The Trashed Criteria shall not parse the comparison operator.
     *
     * @return void
     */
    public function testComparisonOperator(): void
    {
        $operator = ComparisonOperator::getRandomInstance();

        $filterParam = Str::of($this->faker->word())
            ->append('.')
            ->append(TrashedCriteria::PARAM_VALUE)
            ->append('.')
            ->append($operator->key)
            ->__toString();

        $criteria = TrashedCriteria::make($filterParam, $this->faker->word());

        static::assertNull($criteria->getComparisonOperator());
    }

    /**
     * The Trashed Criteria shall not parse the logical operator.
     *
     * @return void
     */
    public function testLogicalOperator(): void
    {
        $operator = BinaryLogicalOperator::getRandomInstance();
        $default = BinaryLogicalOperator::AND();

        $filterParam = Str::of($this->faker->word())
            ->append('.')
            ->append(TrashedCriteria::PARAM_VALUE)
            ->append('.')
            ->append($operator->key)
            ->__toString();

        $criteria = TrashedCriteria::make($filterParam, $this->faker->word());

        static::assertEquals($default, $criteria->getLogicalOperator());
    }
}
