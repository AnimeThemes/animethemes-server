<?php

namespace Tests\Unit\JsonApi\Condition;

use App\Enums\Filter\BinaryLogicalOperator;
use App\Enums\Filter\ComparisonOperator;
use App\JsonApi\Condition\Condition;
use App\JsonApi\QueryParser;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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

        $this->assertEmpty($condition->getScope());
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

        $this->assertEquals($scope, $condition->getScope());
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

        $this->assertEquals($field, $condition->getField());
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

        $this->assertEquals(ComparisonOperator::fromValue(ComparisonOperator::EQ), $condition->getComparisonOperator());
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

        $this->assertEquals($operator, $condition->getComparisonOperator());
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

        $this->assertEquals(BinaryLogicalOperator::fromValue(BinaryLogicalOperator::AND), $condition->getLogicalOperator());
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

        $this->assertEquals($operator, $condition->getLogicalOperator());
    }
}
