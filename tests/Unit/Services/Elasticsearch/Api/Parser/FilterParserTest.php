<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Elasticsearch\Api\Parser;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Criteria\Filter\Expression;
use App\Http\Api\Criteria\Filter\HasCriteria;
use App\Http\Api\Criteria\Filter\Predicate;
use App\Http\Api\Criteria\Filter\TrashedCriteria;
use App\Http\Api\Criteria\Filter\WhereCriteria as BaseWhereCriteria;
use App\Http\Api\Criteria\Filter\WhereInCriteria as BaseWhereInCriteria;
use App\Http\Api\Scope\GlobalScope;
use App\Services\Elasticsearch\Api\Criteria\Filter\WhereCriteria;
use App\Services\Elasticsearch\Api\Criteria\Filter\WhereInCriteria;
use App\Services\Elasticsearch\Api\Parser\FilterParser;
use Illuminate\Foundation\Testing\WithFaker;
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
    public function testWhereCriteria()
    {
        $expression = new Expression($this->faker->word());

        $predicate = new Predicate($this->faker->word(), ComparisonOperator::getRandomInstance(), $expression);

        $criteria = new BaseWhereCriteria($predicate, BinaryLogicalOperator::getRandomInstance(), new GlobalScope());

        static::assertInstanceOf(WhereCriteria::class, FilterParser::parse($criteria));
    }

    /**
     * The Filter Parser shall parse Where In Criteria.
     *
     * @return void
     */
    public function testWhereInCriteria()
    {
        $expression = new Expression($this->faker->word());

        $predicate = new Predicate($this->faker->word(), ComparisonOperator::getRandomInstance(), $expression);

        $criteria = new BaseWhereInCriteria(
            $predicate,
            BinaryLogicalOperator::getRandomInstance(),
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
    public function testHasCriteria()
    {
        $expression = new Expression($this->faker->word());

        $predicate = new Predicate($this->faker->word(), ComparisonOperator::getRandomInstance(), $expression);

        $criteria = new HasCriteria(
            $predicate,
            BinaryLogicalOperator::getRandomInstance(),
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
    public function testTrashedCriteria()
    {
        $expression = new Expression($this->faker->word());

        $predicate = new Predicate($this->faker->word(), ComparisonOperator::getRandomInstance(), $expression);

        $criteria = new TrashedCriteria(
            $predicate,
            BinaryLogicalOperator::getRandomInstance(),
            new GlobalScope()
        );

        static::assertNull(FilterParser::parse($criteria));
    }
}
