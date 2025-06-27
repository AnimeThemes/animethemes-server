<?php

declare(strict_types=1);

namespace Tests\Unit\Scout\Elasticsearch\Api\Parser;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Criteria\Filter\Expression;
use App\Http\Api\Criteria\Filter\HasCriteria;
use App\Http\Api\Criteria\Filter\Predicate;
use App\Http\Api\Criteria\Filter\TrashedCriteria;
use App\Http\Api\Criteria\Filter\WhereCriteria as BaseWhereCriteria;
use App\Http\Api\Criteria\Filter\WhereInCriteria as BaseWhereInCriteria;
use App\Http\Api\Scope\GlobalScope;
use App\Scout\Elasticsearch\Api\Criteria\Filter\WhereCriteria;
use App\Scout\Elasticsearch\Api\Criteria\Filter\WhereInCriteria;
use App\Scout\Elasticsearch\Api\Parser\FilterParser;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

/**
 * Class FilterParserTest.
 */
class FilterParserTest extends TestCase
{
    use WithFaker;

    /**
     * The Filter Parser shall parse Where Criteria.
     *
     * @return void
     */
    public function testWhereCriteria(): void
    {
        $expression = new Expression($this->faker->word());

        $comparisonOperator = Arr::random(ComparisonOperator::cases());

        $predicate = new Predicate($this->faker->word(), $comparisonOperator, $expression);

        $logicalOperator = Arr::random(BinaryLogicalOperator::cases());

        $criteria = new BaseWhereCriteria($predicate, $logicalOperator, new GlobalScope());

        static::assertInstanceOf(WhereCriteria::class, FilterParser::parse($criteria));
    }

    /**
     * The Filter Parser shall parse Where In Criteria.
     *
     * @return void
     */
    public function testWhereInCriteria(): void
    {
        $expression = new Expression($this->faker->word());

        $comparisonOperator = Arr::random(ComparisonOperator::cases());

        $predicate = new Predicate($this->faker->word(), $comparisonOperator, $expression);

        $criteria = new BaseWhereInCriteria(
            $predicate,
            Arr::random(BinaryLogicalOperator::cases()),
            $this->faker->boolean(),
            new GlobalScope()
        );

        static::assertInstanceOf(WhereInCriteria::class, FilterParser::parse($criteria));
    }

    /**
     * The Filter Parser shall not parse Has Criteria.
     *
     * @return void
     */
    public function testHasCriteria(): void
    {
        $expression = new Expression($this->faker->word());

        $comparisonOperator = Arr::random(ComparisonOperator::cases());

        $predicate = new Predicate($this->faker->word(), $comparisonOperator, $expression);

        $criteria = new HasCriteria(
            $predicate,
            Arr::random(BinaryLogicalOperator::cases()),
            new GlobalScope(),
            $this->faker->randomDigitNotNull()
        );

        static::assertNull(FilterParser::parse($criteria));
    }

    /**
     * The Filter Parser shall not parse Trashed Criteria.
     *
     * @return void
     */
    public function testTrashedCriteria(): void
    {
        $expression = new Expression($this->faker->word());

        $comparisonOperator = Arr::random(ComparisonOperator::cases());

        $predicate = new Predicate($this->faker->word(), $comparisonOperator, $expression);

        $criteria = new TrashedCriteria(
            $predicate,
            Arr::random(BinaryLogicalOperator::cases()),
            new GlobalScope()
        );

        static::assertNull(FilterParser::parse($criteria));
    }
}
