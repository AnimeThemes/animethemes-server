<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Enums\Http\Api\Filter\UnaryLogicalOperator;
use App\Http\Api\Criteria\Filter\Criteria;
use App\Http\Api\Criteria\Filter\WhereInCriteria;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tests\TestCase;

class WhereInCriteriaTest extends TestCase
{
    use WithFaker;

    /**
     * The Where In Criteria shall parse the field.
     */
    public function testField(): void
    {
        $field = $this->faker->word();

        $criteria = WhereInCriteria::make(new GlobalScope(), $field, $this->faker->word());

        static::assertEquals($field, $criteria->getField());
    }

    /**
     * The Where In Criteria shall not parse the comparison operator.
     */
    public function testComparisonOperator(): void
    {
        $operator = Arr::random(ComparisonOperator::cases());

        $filterParam = Str::of($this->faker->word())
            ->append('.')
            ->append($this->faker->word())
            ->append('.')
            ->append($operator->name)
            ->__toString();

        $criteria = WhereInCriteria::make(new GlobalScope(), $filterParam, $this->faker->word());

        static::assertNull($criteria->getComparisonOperator());
    }

    /**
     * By default, the Where In Criteria shall use the AND logical operator.
     */
    public function testDefaultLogicalOperator(): void
    {
        $criteria = WhereInCriteria::make(new GlobalScope(), $this->faker->word(), $this->faker->word());

        static::assertEquals(BinaryLogicalOperator::AND, $criteria->getLogicalOperator());
    }

    /**
     * The Where In Criteria shall parse the logical operator.
     */
    public function testLogicalOperator(): void
    {
        $operator = Arr::random(BinaryLogicalOperator::cases());

        $filterParam = Str::of($this->faker->word())->append(Criteria::PARAM_SEPARATOR)->append($operator->name)->__toString();

        $criteria = WhereInCriteria::make(new GlobalScope(), $filterParam, $this->faker->word());

        static::assertEquals($operator, $criteria->getLogicalOperator());
    }

    /**
     * By default, the Where In Criteria shall not use the NOT operator.
     */
    public function testDefaultUnaryOperator(): void
    {
        $criteria = WhereInCriteria::make(new GlobalScope(), $this->faker->word(), $this->faker->word());

        static::assertFalse($criteria->not());
    }

    /**
     * The Where In Criteria shall parse the NOT operator.
     */
    public function testUnaryOperator(): void
    {
        $filterParam = Str::of($this->faker->word())
            ->append(Criteria::PARAM_SEPARATOR)
            ->append($this->faker->word())
            ->append(Criteria::PARAM_SEPARATOR)
            ->append(UnaryLogicalOperator::NOT->value)
            ->__toString();

        $criteria = WhereInCriteria::make(new GlobalScope(), $filterParam, $this->faker->word());

        static::assertTrue($criteria->not());
    }
}
