<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Parser;

use App\Http\Api\Criteria\Filter\HasCriteria;
use App\Http\Api\Criteria\Filter\TrashedCriteria;
use App\Http\Api\Criteria\Filter\WhereCriteria;
use App\Http\Api\Criteria\Filter\WhereInCriteria;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\TypeScope;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class FilterParserTest.
 */
class FilterParserTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Filter Parser shall return no criteria.
     *
     * @return void
     */
    public function test_no_criteria_by_default(): void
    {
        $parameters = [];

        static::assertEmpty(FilterParser::parse($parameters));
    }

    /**
     * The Filter Parser shall parse Trashed Criteria.
     *
     * @return void
     */
    public function test_parse_trashed_criteria(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => $this->faker->word(),
            ],
        ];

        $criteria = FilterParser::parse($parameters)[0];

        static::assertInstanceOf(TrashedCriteria::class, $criteria);
    }

    /**
     * The Filter Parser shall parse Where In criteria.
     *
     * @return void
     */
    public function test_parse_where_in_criteria(): void
    {
        $fields = collect($this->faker()->words());

        $parameters = [
            FilterParser::param() => [
                $this->faker->word() => $fields->join(','),
            ],
        ];

        $criteria = FilterParser::parse($parameters)[0];

        static::assertInstanceOf(WhereInCriteria::class, $criteria);
    }

    /**
     * The Filter Parser shall parse Has Criteria.
     *
     * @return void
     */
    public function test_parse_has_criteria(): void
    {
        $parameters = [
            FilterParser::param() => [
                HasCriteria::PARAM_VALUE => $this->faker->word(),
            ],
        ];

        $criteria = FilterParser::parse($parameters)[0];

        static::assertInstanceOf(HasCriteria::class, $criteria);
    }

    /**
     * The Filter Parser shall parse Where criteria.
     *
     * @return void
     */
    public function test_parse_where_criteria(): void
    {
        $parameters = [
            FilterParser::param() => [
                $this->faker->word() => $this->faker->word(),
            ],
        ];

        $criteria = FilterParser::parse($parameters)[0];

        static::assertInstanceOf(WhereCriteria::class, $criteria);
    }

    /**
     * The Filter Parser shall parse a global scope if scope is not provided.
     *
     * @return void
     */
    public function test_parse_global_scope(): void
    {
        $parameters = [
            FilterParser::param() => [
                $this->faker->word() => $this->faker->word(),
            ],
        ];

        $criteria = FilterParser::parse($parameters)[0];

        static::assertInstanceOf(GlobalScope::class, $criteria->getScope());
    }

    /**
     * The Filter Parser shall parse a scope if provided.
     *
     * @return void
     */
    public function test_parse_type_scope(): void
    {
        $type = Str::singular($this->faker->word());

        $parameters = [
            FilterParser::param() => [
                $type => [
                    $this->faker->word() => $this->faker->word(),
                ],
            ],
        ];

        $criteria = FilterParser::parse($parameters)[0];

        $scope = $criteria->getScope();

        static::assertTrue($scope instanceof TypeScope && $scope->getType() === $type);
    }
}
