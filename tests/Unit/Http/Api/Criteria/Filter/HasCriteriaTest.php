<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Criteria\Filter\HasCriteria;
use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\TypeScope;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class HasCriteriaTest.
 */
class HasCriteriaTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Has Criteria shall be scoped globally.
     *
     * @return void
     */
    public function testGlobalScope()
    {
        $criteria = HasCriteria::make($this->faker->word(), $this->faker->word());

        static::assertInstanceOf(GlobalScope::class, $criteria->getScope());
    }

    /**
     * The Has Criteria shall parse the scope if provided.
     *
     * @return void
     */
    public function testScope()
    {
        $type = Str::singular($this->faker->word());

        $filterParam = Str::of($type)->append('.')->append(HasCriteria::PARAM_VALUE)->__toString();

        $criteria = HasCriteria::make($filterParam, $this->faker->word());

        $scope = $criteria->getScope();

        static::assertTrue($scope instanceof TypeScope && $scope->getType() === $type);
    }

    /**
     * The Has Criteria shall parse the field.
     *
     * @return void
     */
    public function testField()
    {
        $criteria = HasCriteria::make(HasCriteria::PARAM_VALUE, $this->faker->word());

        static::assertEquals(HasCriteria::PARAM_VALUE, $criteria->getField());
    }

    /**
     * By default, the Has Criteria shall use the GTE comparison operator.
     *
     * @return void
     */
    public function testDefaultComparisonOperator()
    {
        $criteria = HasCriteria::make(HasCriteria::PARAM_VALUE, $this->faker->word());

        static::assertEquals(ComparisonOperator::GTE(), $criteria->getComparisonOperator());
    }

    /**
     * The Has Criteria shall parse the comparison operator.
     *
     * @return void
     */
    public function testComparisonOperator()
    {
        $operator = ComparisonOperator::getRandomInstance();

        $filterParam = Str::of(HasCriteria::PARAM_VALUE)->append('.')->append($operator->key)->__toString();

        $criteria = HasCriteria::make($filterParam, $this->faker->word());

        static::assertEquals($operator, $criteria->getComparisonOperator());
    }

    /**
     * By default, the Has Criteria shall have a count of 1.
     *
     * @return void
     */
    public function testDefaultCount()
    {
        $criteria = HasCriteria::make(HasCriteria::PARAM_VALUE, $this->faker->word());

        static::assertEquals(1, $criteria->getCount());
    }

    /**
     * The Has Criteria shall parse count.
     *
     * @return void
     */
    public function testCount()
    {
        $count = $this->faker->randomDigitNotNull();

        $filterParam = Str::of(HasCriteria::PARAM_VALUE)->append('.')->append($count)->__toString();

        $criteria = HasCriteria::make($filterParam, $this->faker->word());

        static::assertEquals($count, $criteria->getCount());
    }

    /**
     * By default, the Has Criteria shall use the AND logical operator.
     *
     * @return void
     */
    public function testDefaultLogicalOperator()
    {
        $criteria = HasCriteria::make($this->faker->word(), $this->faker->word());

        static::assertEquals(BinaryLogicalOperator::AND(), $criteria->getLogicalOperator());
    }

    /**
     * The Has Criteria shall parse the logical operator.
     *
     * @return void
     */
    public function testLogicalOperator()
    {
        $operator = BinaryLogicalOperator::getRandomInstance();

        $filterParam = Str::of(HasCriteria::PARAM_VALUE)->append('.')->append($operator->key)->__toString();

        $criteria = HasCriteria::make($filterParam, $this->faker->word());

        static::assertEquals($operator, $criteria->getLogicalOperator());
    }
}
