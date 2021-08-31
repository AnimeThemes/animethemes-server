<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Parser;

use App\Http\Api\Criteria\Filter\HasCriteria;
use App\Http\Api\Criteria\Filter\TrashedCriteria;
use App\Http\Api\Criteria\Filter\WhereCriteria;
use App\Http\Api\Criteria\Filter\WhereInCriteria;
use App\Http\Api\Parser\FilterParser;
use Illuminate\Foundation\Testing\WithFaker;
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
    public function testNoCriteriaByDefault()
    {
        $parameters = [];

        static::assertEmpty(FilterParser::parse($parameters));
    }

    /**
     * The Filter Parser shall parse Trashed Criteria.
     *
     * @return void
     */
    public function testParseTrashedCriteria()
    {
        $parameters = [
            FilterParser::$param => [
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
    public function testParseWhereInCriteria()
    {
        $fields = collect($this->faker()->words());

        $parameters = [
            FilterParser::$param => [
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
    public function testParseHasCriteria()
    {
        $parameters = [
            FilterParser::$param => [
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
    public function testParseWhereCriteria()
    {
        $parameters = [
            FilterParser::$param => [
                $this->faker->word() => $this->faker->word(),
            ],
        ];

        $criteria = FilterParser::parse($parameters)[0];

        static::assertInstanceOf(WhereCriteria::class, $criteria);
    }
}
