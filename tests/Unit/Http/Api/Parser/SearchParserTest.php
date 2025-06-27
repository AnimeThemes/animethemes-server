<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Parser;

use App\Http\Api\Criteria\Search\Criteria;
use App\Http\Api\Parser\SearchParser;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class SearchParserTest.
 */
class SearchParserTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Search Parser shall return no criteria.
     *
     * @return void
     */
    public function testNoCriteriaByDefault(): void
    {
        $parameters = [];

        static::assertEmpty(SearchParser::parse($parameters));
    }

    /**
     * The Search parser shall parse the search query.
     *
     * @return void
     */
    public function testParseSearchCriteria(): void
    {
        $parameters = [
            SearchParser::param() => $this->faker->word(),
        ];

        $criteria = SearchParser::parse($parameters)[0];

        static::assertInstanceOf(Criteria::class, $criteria);
    }

    /**
     * The Search parser shall parse the search term.
     *
     * @return void
     */
    public function testParseSearchCriteriaTerm(): void
    {
        $term = $this->faker->word();

        $parameters = [
            SearchParser::param() => $term,
        ];

        $criteria = SearchParser::parse($parameters)[0];

        static::assertEquals($term, $criteria->getTerm());
    }
}
