<?php

declare(strict_types=1);

namespace JsonApi\Condition;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Condition\Condition;
use App\Http\Api\Condition\TrashedCondition;
use App\Http\Api\Condition\WhereCondition;
use App\Http\Api\Condition\WhereInCondition;
use App\Http\Api\QueryParser;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class ConditionTest.
 */
class ConditionTest extends TestCase
{
    use WithFaker;

    /**
     * The condition shall resolve the predicate field.
     *
     * @return void
     */
    public function testGetField()
    {
        $field = $this->faker->word();

        $condition = Condition::make($field, $this->faker->word());

        static::assertEquals($field, $condition->getField());
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

        static::assertEquals($operator, $condition->getComparisonOperator());
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

        static::assertEquals([$value], $condition->getFilterValues());
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

        static::assertEquals(Str::of($values)->explode(',')->all(), $condition->getFilterValues());
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

        static::assertInstanceOf(WhereInCondition::class, $condition);
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

        static::assertInstanceOf(WhereCondition::class, $condition);
    }

    /**
     * A Trashed Condition shall be created for the trashed filter key.
     *
     * @return void
     */
    public function testMakeTrashedCondition()
    {
        $value = $this->faker->word();

        $condition = Condition::make('trashed', $value);

        static::assertInstanceOf(TrashedCondition::class, $condition);
    }
}
