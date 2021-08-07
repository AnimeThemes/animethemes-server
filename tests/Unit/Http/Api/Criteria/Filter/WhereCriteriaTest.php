<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Criteria\Field;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Criteria\Filter\WhereCriteria;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class WhereCriteriaTest.
 */
class WhereCriteriaTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Where Criteria shall be scoped globally.
     *
     * @return void
     */
    public function testGlobalScope()
    {
        $criteria = WhereCriteria::make($this->faker->word(), $this->faker->word());

        static::assertEmpty($criteria->getScope());
    }

    /**
     * The Where Criteria shall parse the scope if provided.
     *
     * @return void
     */
    public function testScope()
    {
        $scope = $this->faker->word();

        $filterParam = Str::of($scope)->append('.')->append($this->faker->word())->__toString();

        $criteria = WhereCriteria::make($filterParam, $this->faker->word());

        static::assertEquals($scope, $criteria->getScope());
    }

    /**
     * The Where Criteria shall parse the field.
     *
     * @return void
     */
    public function testField()
    {
        $field = $this->faker->word();

        $criteria = WhereCriteria::make($field, $this->faker->word());

        static::assertEquals($field, $criteria->getField());
    }

    /**
     * By default, the Where Criteria shall use the EQ comparison operator
     *
     * @return void
     */
    public function testDefaultComparisonOperator()
    {
        $criteria = WhereCriteria::make($this->faker->word(), $this->faker->word());

        static::assertEquals(ComparisonOperator::EQ(), $criteria->getComparisonOperator());
    }

    /**
     * The Where Criteria shall parse the comparison operator
     *
     * @return void
     */
    public function testComparisonOperator()
    {
        $operator = ComparisonOperator::getRandomInstance();

        $filterParam = Str::of($this->faker->word())->append('.')->append($operator->key)->__toString();

        $criteria = WhereCriteria::make($filterParam, $this->faker->word());

        static::assertEquals($operator, $criteria->getComparisonOperator());
    }

    /**
     * By default, the Where Criteria shall use the AND logical operator.
     *
     * @return void
     */
    public function testDefaultLogicalOperator()
    {
        $criteria = WhereCriteria::make($this->faker->word(), $this->faker->word());

        static::assertEquals(BinaryLogicalOperator::AND(), $criteria->getLogicalOperator());
    }

    /**
     * The Where Criteria shall parse the logical operator.
     *
     * @return void
     */
    public function testLogicalOperator()
    {
        $operator = BinaryLogicalOperator::getRandomInstance();

        $filterParam = Str::of($this->faker->word())->append('.')->append($operator->key)->__toString();

        $criteria = WhereCriteria::make($filterParam, $this->faker->word());

        static::assertEquals($operator, $criteria->getLogicalOperator());
    }
}
