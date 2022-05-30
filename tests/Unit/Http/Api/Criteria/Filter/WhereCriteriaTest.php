<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Criteria\Filter\Criteria;
use App\Http\Api\Criteria\Filter\WhereCriteria;
use App\Http\Api\Scope\GlobalScope;
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
     * The Where Criteria shall parse the field.
     *
     * @return void
     */
    public function testField(): void
    {
        $field = $this->faker->word();

        $criteria = WhereCriteria::make(new GlobalScope(), $field, $this->faker->word());

        static::assertEquals($field, $criteria->getField());
    }

    /**
     * By default, the Where Criteria shall use the EQ comparison operator.
     *
     * @return void
     */
    public function testDefaultComparisonOperator(): void
    {
        $criteria = WhereCriteria::make(new GlobalScope(), $this->faker->word(), $this->faker->word());

        static::assertEquals(ComparisonOperator::EQ(), $criteria->getComparisonOperator());
    }

    /**
     * The Where Criteria shall parse the comparison operator.
     *
     * @return void
     */
    public function testComparisonOperator(): void
    {
        $operator = ComparisonOperator::getRandomInstance();

        $filterParam = Str::of($this->faker->word())->append(Criteria::PARAM_SEPARATOR)->append($operator->key)->__toString();

        $criteria = WhereCriteria::make(new GlobalScope(), $filterParam, $this->faker->word());

        static::assertEquals($operator, $criteria->getComparisonOperator());
    }

    /**
     * By default, the Where Criteria shall use the AND logical operator.
     *
     * @return void
     */
    public function testDefaultLogicalOperator(): void
    {
        $criteria = WhereCriteria::make(new GlobalScope(), $this->faker->word(), $this->faker->word());

        static::assertEquals(BinaryLogicalOperator::AND(), $criteria->getLogicalOperator());
    }

    /**
     * The Where Criteria shall parse the logical operator.
     *
     * @return void
     */
    public function testLogicalOperator(): void
    {
        $operator = BinaryLogicalOperator::getRandomInstance();

        $filterParam = Str::of($this->faker->word())->append(Criteria::PARAM_SEPARATOR)->append($operator->key)->__toString();

        $criteria = WhereCriteria::make(new GlobalScope(), $filterParam, $this->faker->word());

        static::assertEquals($operator, $criteria->getLogicalOperator());
    }
}
