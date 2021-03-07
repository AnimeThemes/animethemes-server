<?php

namespace Tests\Unit\JsonApi\Condition;

use App\Enums\Filter\ComparisonOperator;
use App\JsonApi\Condition\Condition;
use App\JsonApi\Condition\WhereCondition;
use App\JsonApi\Condition\WhereDateCondition;
use App\JsonApi\Condition\WhereInCondition;
use App\JsonApi\QueryParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class ConditionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The condition shall resolve the predicate field.
     *
     * @return void
     */
    public function testGetField()
    {
        $field = $this->faker->word();

        $condition = Condition::make($field, $this->faker->word());

        $this->assertEquals($field, $condition->getField());
    }

    /**
     * The condition shall resolve the predicate comparison operator.
     *
     * @return void
     */
    public function testGetComparisonOperator()
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
     * The condition shall return wrapped scalar values.
     *
     * @return void
     */
    public function testGetScalarFilterValue()
    {
        $field = $this->faker->word();

        $value = $this->faker->word();

        $condition = Condition::make($field, $value);

        $this->assertequals([$value], $condition->getFilterValues());
    }

    /**
     * The condition shall return collections of values as an array.
     *
     * @return void
     */
    public function testGetCollectionFilterValues()
    {
        $field = $this->faker->word();

        $values = collect($this->faker->words())->join(',');

        $condition = Condition::make($field, $values);

        $this->assertequals(Str::of($values)->explode(',')->all(), $condition->getFilterValues());
    }

    /**
     * A Where In Condition shall be created for multi-value filters.
     *
     * @return void
     */
    public function testMakeWhereInCondition()
    {
        $field = $this->faker->word();

        $values = collect($this->faker->words())->join(',');

        $condition = Condition::make($field, $values);

        $this->assertInstanceOf(WhereInCondition::class, $condition);
    }

    /**
     * A Where Date Condition shall be created for date filters.
     *
     * @return void
     */
    public function testMakeWhereDateCondition()
    {
        $field = $this->faker->word();

        $value = $this->faker->date();

        $condition = Condition::make($field, $value);

        $this->assertInstanceOf(WhereDateCondition::class, $condition);
    }

    /**
     * A Where Condition shall be created for single-value filters.
     *
     * @return void
     */
    public function testMakeWhereCondition()
    {
        $field = $this->faker->word();

        $value = $this->faker->word();

        $condition = Condition::make($field, $value);

        $this->assertInstanceOf(WhereCondition::class, $condition);
    }
}
