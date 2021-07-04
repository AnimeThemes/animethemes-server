<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Condition;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Condition\Condition;
use App\Http\Api\QueryParser;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class WhereConditionTest.
 */
class WhereConditionTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Where Condition shall be scoped globally.
     *
     * @return void
     */
    public function testGlobalScope()
    {
        $field = $this->faker->word();

        $condition = Condition::make($field, $this->faker->word());

        static::assertEmpty($condition->getScope());
    }

    /**
     * The Where Condition shall parse scope from the query.
     *
     * @return void
     */
    public function testScope()
    {
        $scope = $this->faker->word();

        $field = $this->faker->word();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $scope => [
                    $field => $this->faker->word(),
                ],
            ],
        ];

        $parser = QueryParser::make($parameters);

        $condition = collect($parser->getConditions($field))->first();

        static::assertEquals($scope, $condition->getScope());
    }

    /**
     * The Where Condition shall parse the field from the query.
     *
     * @return void
     */
    public function testField()
    {
        $field = $this->faker->word();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $field => $this->faker->word(),
            ],
        ];

        $parser = QueryParser::make($parameters);

        $condition = collect($parser->getConditions($field))->first();

        static::assertEquals($field, $condition->getField());
    }

    /**
     * By default, the Where Condition shall use the EQ comparison operator.
     *
     * @return void
     */
    public function testDefaultComparisonOperator()
    {
        $field = $this->faker->word();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $field => $this->faker->word(),
            ],
        ];

        $parser = QueryParser::make($parameters);

        $condition = collect($parser->getConditions($field))->first();

        static::assertEquals(ComparisonOperator::EQ(), $condition->getComparisonOperator());
    }

    /**
     * The Where Condition shall parse the comparison operator.
     *
     * @return void
     */
    public function testComparisonOperator()
    {
        $field = $this->faker->word();

        $operator = ComparisonOperator::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $field => [
                    $operator->key => $this->faker->word(),
                ],
            ],
        ];

        $parser = QueryParser::make($parameters);

        $condition = collect($parser->getConditions($field))->first();

        static::assertEquals($operator, $condition->getComparisonOperator());
    }

    /**
     * By default, the Where Condition shall use the AND logical operator.
     *
     * @return void
     */
    public function testDefaultLogicalOperator()
    {
        $field = $this->faker->word();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $field => $this->faker->word(),
            ],
        ];

        $parser = QueryParser::make($parameters);

        $condition = collect($parser->getConditions($field))->first();

        static::assertEquals(BinaryLogicalOperator::AND(), $condition->getLogicalOperator());
    }

    /**
     * The Where Condition shall parse the logical operator.
     *
     * @return void
     */
    public function testLogicalOperator()
    {
        $field = $this->faker->word();

        $operator = BinaryLogicalOperator::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $field => [
                    $operator->key => $this->faker->word(),
                ],
            ],
        ];

        $parser = QueryParser::make($parameters);

        $condition = collect($parser->getConditions($field))->first();

        static::assertEquals($operator, $condition->getLogicalOperator());
    }
}
