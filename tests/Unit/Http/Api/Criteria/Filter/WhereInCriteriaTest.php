<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Criteria\Field;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Enums\Http\Api\Filter\UnaryLogicalOperator;
use App\Http\Api\Criteria\Filter\WhereInCriteria;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class WhereInCriteriaTest.
 */
class WhereInCriteriaTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Where In Criteria shall be scoped globally.
     *
     * @return void
     */
    public function testGlobalScope()
    {
        $criteria = WhereInCriteria::make($this->faker->word(), $this->faker->word());

        static::assertEmpty($criteria->getScope());
    }

    /**
     * The Where In Criteria shall parse the scope if provided.
     *
     * @return void
     */
    public function testScope()
    {
        $scope = $this->faker->word();

        $filterParam = Str::of($scope)->append('.')->append($this->faker->word())->__toString();

        $criteria = WhereInCriteria::make($filterParam, $this->faker->word());

        static::assertEquals($scope, $criteria->getScope());
    }

    /**
     * The Where In Criteria shall parse the field.
     *
     * @return void
     */
    public function testField()
    {
        $field = $this->faker->word();

        $criteria = WhereInCriteria::make($field, $this->faker->word());

        static::assertEquals($field, $criteria->getField());
    }

    /**
     * The Where In Criteria shall not parse the comparison operator.
     *
     * @return void
     */
    public function testComparisonOperator()
    {
        $operator = ComparisonOperator::getRandomInstance();

        $filterParam = Str::of($this->faker->word())
            ->append('.')
            ->append($this->faker->word())
            ->append('.')
            ->append($operator->key)
            ->__toString();

        $criteria = WhereInCriteria::make($filterParam, $this->faker->word());

        static::assertNull($criteria->getComparisonOperator());
    }

    /**
     * By default, the Where In Criteria shall use the AND logical operator.
     *
     * @return void
     */
    public function testDefaultLogicalOperator()
    {
        $criteria = WhereInCriteria::make($this->faker->word(), $this->faker->word());

        static::assertEquals(BinaryLogicalOperator::AND(), $criteria->getLogicalOperator());
    }

    /**
     * The Where In Criteria shall parse the logical operator.
     *
     * @return void
     */
    public function testLogicalOperator()
    {
        $operator = BinaryLogicalOperator::getRandomInstance();

        $filterParam = Str::of($this->faker->word())->append('.')->append($operator->key)->__toString();

        $criteria = WhereInCriteria::make($filterParam, $this->faker->word());

        static::assertEquals($operator, $criteria->getLogicalOperator());
    }

    /**
     * By default, the Where In Criteria shall not use the NOT operator.
     *
     * @return void
     */
    public function testDefaultUnaryOperator()
    {
        $criteria = WhereInCriteria::make($this->faker->word(), $this->faker->word());

        static::assertFalse($criteria->not());
    }

    /**
     * The Where In Criteria shall parse the NOT operator.
     *
     * @return void
     */
    public function testUnaryOperator()
    {
        $filterParam = Str::of($this->faker->word())
            ->append('.')
            ->append($this->faker->word())
            ->append('.')
            ->append(UnaryLogicalOperator::NOT)
            ->__toString();

        $criteria = WhereInCriteria::make($filterParam, $this->faker->word());

        static::assertTrue($criteria->not());
    }
}
