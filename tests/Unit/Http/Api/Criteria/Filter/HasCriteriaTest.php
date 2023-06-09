<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Criteria\Filter\Criteria;
use App\Http\Api\Criteria\Filter\HasCriteria;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class HasCriteriaTest.
 */
class HasCriteriaTest extends TestCase
{
    use WithFaker;

    /**
     * The Has Criteria shall parse the field.
     *
     * @return void
     */
    public function testField(): void
    {
        $criteria = HasCriteria::make(new GlobalScope(), HasCriteria::PARAM_VALUE, $this->faker->word());

        static::assertEquals(HasCriteria::PARAM_VALUE, $criteria->getField());
    }

    /**
     * By default, the Has Criteria shall use the GTE comparison operator.
     *
     * @return void
     */
    public function testDefaultComparisonOperator(): void
    {
        $criteria = HasCriteria::make(new GlobalScope(), HasCriteria::PARAM_VALUE, $this->faker->word());

        static::assertEquals(ComparisonOperator::GTE, $criteria->getComparisonOperator());
    }

    /**
     * The Has Criteria shall parse the comparison operator.
     *
     * @return void
     */
    public function testComparisonOperator(): void
    {
        $operator = Arr::random(ComparisonOperator::cases());

        $filterParam = Str::of(HasCriteria::PARAM_VALUE)->append(Criteria::PARAM_SEPARATOR)->append($operator->name)->__toString();

        $criteria = HasCriteria::make(new GlobalScope(), $filterParam, $this->faker->word());

        static::assertEquals($operator, $criteria->getComparisonOperator());
    }

    /**
     * By default, the Has Criteria shall have a count of 1.
     *
     * @return void
     */
    public function testDefaultCount(): void
    {
        $criteria = HasCriteria::make(new GlobalScope(), HasCriteria::PARAM_VALUE, $this->faker->word());

        static::assertEquals(1, $criteria->getCount());
    }

    /**
     * The Has Criteria shall parse count.
     *
     * @return void
     */
    public function testCount(): void
    {
        $count = $this->faker->randomDigitNotNull();

        $filterParam = Str::of(HasCriteria::PARAM_VALUE)->append(Criteria::PARAM_SEPARATOR)->append(strval($count))->__toString();

        $criteria = HasCriteria::make(new GlobalScope(), $filterParam, $this->faker->word());

        static::assertEquals($count, $criteria->getCount());
    }

    /**
     * By default, the Has Criteria shall use the AND logical operator.
     *
     * @return void
     */
    public function testDefaultLogicalOperator(): void
    {
        $criteria = HasCriteria::make(new GlobalScope(), $this->faker->word(), $this->faker->word());

        static::assertEquals(BinaryLogicalOperator::AND, $criteria->getLogicalOperator());
    }

    /**
     * The Has Criteria shall parse the logical operator.
     *
     * @return void
     */
    public function testLogicalOperator(): void
    {
        $operator = Arr::random(BinaryLogicalOperator::cases());

        $filterParam = Str::of(HasCriteria::PARAM_VALUE)->append(Criteria::PARAM_SEPARATOR)->append($operator->name)->__toString();

        $criteria = HasCriteria::make(new GlobalScope(), $filterParam, $this->faker->word());

        static::assertEquals($operator, $criteria->getLogicalOperator());
    }
}
