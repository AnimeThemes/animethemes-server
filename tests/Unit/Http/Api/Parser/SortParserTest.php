<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Parser;

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Sort\FieldCriteria;
use App\Http\Api\Criteria\Sort\RandomCriteria;
use App\Http\Api\Criteria\Sort\RelationCriteria;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\TypeScope;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class SortParserTest.
 */
class SortParserTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Sort Parser shall return no criteria.
     *
     * @return void
     */
    public function testNoCriteriaByDefault(): void
    {
        $parameters = [];

        static::assertEmpty(SortParser::parse($parameters));
    }

    /**
     * The Sort Parser shall parse Random Criteria.
     *
     * @return void
     */
    public function testParseRandomCriteria(): void
    {
        $parameters = [
            SortParser::param() => RandomCriteria::PARAM_VALUE,
        ];

        $criteria = SortParser::parse($parameters)[0];

        static::assertInstanceOf(RandomCriteria::class, $criteria);
    }

    /**
     * The Sort Parser shall parse Relation Criteria.
     *
     * @return void
     */
    public function testParseRelationCriteria(): void
    {
        $parameters = [
            SortParser::param() => collect($this->faker->words())->join('.'),
        ];

        $criteria = SortParser::parse($parameters)[0];

        static::assertInstanceOf(RelationCriteria::class, $criteria);
    }

    /**
     * The Sort Parser shall parse Field Criteria.
     *
     * @return void
     */
    public function testParseFieldCriteria(): void
    {
        $parameters = [
            SortParser::param() => $this->faker->word(),
        ];

        $criteria = SortParser::parse($parameters)[0];

        static::assertInstanceOf(FieldCriteria::class, $criteria);
    }

    /**
     * The Sort Parser shall parse the sort field.
     *
     * @return void
     */
    public function testParseCriteriaField(): void
    {
        $field = $this->faker->word();

        $parameters = [
            SortParser::param() => $field,
        ];

        $criteria = SortParser::parse($parameters)[0];

        static::assertEquals($field, $criteria->getField());
    }

    /**
     * By default, the Sort Parser shall parse an ascending sort direction.
     *
     * @return void
     */
    public function testParseDefaultDirection(): void
    {
        $parameters = [
            SortParser::param() => $this->faker->word(),
        ];

        $criteria = SortParser::parse($parameters)[0];

        static::assertTrue(
            $criteria instanceof FieldCriteria
            && $criteria->getDirection() === Direction::ASCENDING
        );
    }

    /**
     * The Sort Parser shall parse a descending direction on fields that prepend a '-'.
     *
     * @return void
     */
    public function testParseDescendingDirection(): void
    {
        $field = Str::of('-')->append($this->faker->word())->__toString();

        $parameters = [
            SortParser::param() => $field,
        ];

        $criteria = SortParser::parse($parameters)[0];

        static::assertTrue(
            $criteria instanceof FieldCriteria
            && $criteria->getDirection() === Direction::DESCENDING
        );
    }

    /**
     * The Sort Parser shall parse a global scope if scope is not provided.
     *
     * @return void
     */
    public function testParseGlobalScope(): void
    {
        $parameters = [
            SortParser::param() => $this->faker->word(),
        ];

        $criteria = SortParser::parse($parameters)[0];

        static::assertInstanceOf(GlobalScope::class, $criteria->getScope());
    }

    /**
     * The Sort Parser shall parse a scope if provided.
     *
     * @return void
     */
    public function testParseTypeScope(): void
    {
        $type = Str::singular($this->faker->word());

        $parameters = [
            SortParser::param() => [
                $type => $this->faker->word(),
            ],
        ];

        $criteria = SortParser::parse($parameters)[0];

        $scope = $criteria->getScope();

        static::assertTrue($scope instanceof TypeScope && $scope->getType() === $type);
    }
}
