<?php

declare(strict_types=1);

namespace JsonApi;

use App\JsonApi\QueryParser;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

/**
 * Class QueryParserTest.
 */
class QueryParserTest extends TestCase
{
    use WithFaker;

    /**
     * By default, all fields are allowed for every type if sparse fieldsets are not specified.
     *
     * @return void
     */
    public function testAllFieldsAllowedByDefault()
    {
        $type = $this->faker->word();
        $fields = $this->faker->words($this->faker->randomDigitNotNull);

        $parameters = [];

        $parser = QueryParser::make($parameters);

        foreach ($fields as $field) {
            static::assertTrue($parser->isAllowedField($type, $field));
        }
    }

    /**
     * If no fields are specified for the type, no fields are allowed.
     *
     * @return void
     */
    public function testNoFieldsAllowedIfEmpty()
    {
        $type = $this->faker->word();
        $fields = $this->faker->words($this->faker->randomDigitNotNull);

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                $type => '',
            ],
        ];

        $parser = QueryParser::make($parameters);

        foreach ($fields as $field) {
            static::assertFalse($parser->isAllowedField($type, $field));
        }
    }

    /**
     * Fields shall be allowed if included in the sparse fieldsets for its type.
     *
     * @return void
     */
    public function testFieldIsAllowed()
    {
        $type = $this->faker->word();
        $fields = $this->faker->words($this->faker->randomDigitNotNull);
        $allowedField = Arr::random($fields);

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                $type => $allowedField,
            ],
        ];

        $parser = QueryParser::make($parameters);

        foreach ($fields as $field) {
            if ($field == $allowedField) {
                static::assertTrue($parser->isAllowedField($type, $field));
            } else {
                static::assertFalse($parser->isAllowedField($type, $field));
            }
        }
    }

    /**
     * By default, no include paths are allowed if include paths are not specified.
     *
     * @return void
     */
    public function testNoAllowedIncludesByDefault()
    {
        $includes = $this->faker->words($this->faker->randomDigitNotNull);

        $parameters = [];

        $parser = QueryParser::make($parameters);

        static::assertEmpty($parser->getIncludePaths($includes));
    }

    /**
     * If no include paths are specified, no include paths are allowed.
     *
     * @return void
     */
    public function testNoIncludesAllowedIfEmpty()
    {
        $includes = $this->faker->words($this->faker->randomDigitNotNull);

        $parameters = [
            QueryParser::PARAM_INCLUDE => '',
        ];

        $parser = QueryParser::make($parameters);

        static::assertEmpty($parser->getIncludePaths($includes));
    }

    /**
     * If include paths are specified, those within the allowed include list are kept.
     *
     * @return void
     */
    public function testIncludeAllowedPaths()
    {
        $includeCount = $this->faker->randomDigitNotNull;
        $includes = $this->faker->words($includeCount * 2);
        $allowedIncludes = Arr::random($includes, $includeCount);

        $parameters = [
            QueryParser::PARAM_INCLUDE => implode(',', $includes),
        ];

        $parser = QueryParser::make($parameters);

        static::assertEmpty(array_diff($allowedIncludes, $parser->getIncludePaths($allowedIncludes)));
    }

    /**
     * By default, no include paths are allowed if selected include paths are not specified.
     *
     * @return void
     */
    public function testNoAllowedResourceIncludesByDefault()
    {
        $type = $this->faker->word();
        $includes = $this->faker->words($this->faker->randomDigitNotNull);

        $parameters = [];

        $parser = QueryParser::make($parameters);

        static::assertEmpty($parser->getIncludePaths($includes, $type));
    }

    /**
     * If no include paths are specified, no include paths are allowed.
     *
     * @return void
     */
    public function testNoResourceIncludesAllowedIfEmpty()
    {
        $type = $this->faker->word();
        $includes = $this->faker->words($this->faker->randomDigitNotNull);

        $parameters = [
            QueryParser::PARAM_INCLUDE => [
                $type => '',
            ],
        ];

        $parser = QueryParser::make($parameters);

        static::assertEmpty($parser->getIncludePaths($includes, $type));
    }

    /**
     * If resource include paths are specified, those within the allowed include list for the resource are kept.
     *
     * @return void
     */
    public function testResourceIncludeAllowedPaths()
    {
        $type = $this->faker->word();
        $includeCount = $this->faker->randomDigitNotNull;
        $includes = $this->faker->words($includeCount * 2);
        $allowedIncludes = Arr::random($includes, $includeCount);

        $parameters = [
            QueryParser::PARAM_INCLUDE => [
                $type => implode(',', $includes),
            ],
        ];

        $parser = QueryParser::make($parameters);

        static::assertEmpty(array_diff($allowedIncludes, $parser->getIncludePaths($allowedIncludes, $type)));
    }

    /**
     * Sorts that are not prefixed with '+' and '-' are parsed as ascending.
     *
     * @return void
     */
    public function testSortsAreParsed()
    {
        $sorts = $this->faker->unique()->words($this->faker->randomDigitNotNull);

        $parameters = [
            QueryParser::PARAM_SORT => implode(',', $sorts),
        ];

        $parser = QueryParser::make($parameters);
        $parserSorts = $parser->getSorts();

        foreach ($sorts as $sort) {
            static::assertTrue(Arr::get($parserSorts, $sort));
        }
    }

    /**
     * Sorts that are not prefixed with '-' are parsed as descending.
     *
     * @return void
     */
    public function testDescSortsAreParsed()
    {
        $sortsDesc = $this->faker->unique()->words($this->faker->randomDigitNotNull);
        $sortsDescSymbol = array_map(function (string $sort) {
            return '-'.$sort;
        }, $sortsDesc);

        $parameters = [
            QueryParser::PARAM_SORT => implode(',', $sortsDescSymbol),
        ];

        $parser = QueryParser::make($parameters);
        $parserSorts = $parser->getSorts();

        foreach ($sortsDesc as $sort) {
            static::assertFalse(Arr::get($parserSorts, $sort));
        }
    }

    /**
     * Sorts that are not prefixed with '+' are parsed as ascending.
     *
     * @return void
     */
    public function testAscSortsAreParsed()
    {
        $sortsAsc = $this->faker->unique()->words($this->faker->randomDigitNotNull);
        $sortsAscSymbol = array_map(function (string $sort) {
            return '+'.$sort;
        }, $sortsAsc);

        $parameters = [
            QueryParser::PARAM_SORT => implode(',', $sortsAscSymbol),
        ];

        $parser = QueryParser::make($parameters);
        $parserSorts = $parser->getSorts();

        foreach ($sortsAsc as $sort) {
            static::assertTrue(Arr::get($parserSorts, $sort));
        }
    }

    /**
     * The parser shall indicate if a field has a filter provided.
     *
     * @return void
     */
    public function testHasFilter()
    {
        $fields = $this->faker->words($this->faker->randomDigitNotNull);
        $filterField = Arr::random($fields);
        $filter = $this->faker->word();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filterField => $filter,
            ],
        ];

        $parser = QueryParser::make($parameters);

        foreach ($fields as $field) {
            if ($field == $filterField) {
                static::assertTrue($parser->hasCondition($field));
            } else {
                static::assertFalse($parser->hasCondition($field));
            }
        }
    }

    /**
     * The parser shall collect filter values for a field.
     *
     * @return void
     */
    public function testGetFilters()
    {
        $fields = $this->faker->words($this->faker->randomDigitNotNull);
        $filterField = Arr::random($fields);
        $filter = $this->faker->word();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filterField => $filter,
            ],
        ];

        $parser = QueryParser::make($parameters);

        foreach ($fields as $field) {
            if ($field == $filterField) {
                static::assertNotEmpty($parser->getConditions($field));
            } else {
                static::assertEmpty($parser->getConditions($field));
            }
        }
    }

    /**
     * The parser will indicate that our request doesn't have a search value if not specified.
     *
     * @return void
     */
    public function testDoesNotHaveSearch()
    {
        $parameters = [
            QueryParser::PARAM_SEARCH => '',
        ];

        $parser = QueryParser::make($parameters);

        static::assertFalse($parser->hasSearch());
    }

    /**
     * The parser will indicate that our request has a search value if specified.
     *
     * @return void
     */
    public function testHasSearch()
    {
        $parameters = [
            QueryParser::PARAM_SEARCH => $this->faker->word(),
        ];

        $parser = QueryParser::make($parameters);

        static::assertTrue($parser->hasSearch());
    }

    /**
     * The parser shall collect the search query.
     *
     * @return void
     */
    public function testGetSearch()
    {
        $q = $this->faker->word();

        $parameters = [
            QueryParser::PARAM_SEARCH => $q,
        ];

        $parser = QueryParser::make($parameters);

        static::assertEquals($q, $parser->getSearch());
    }

    /**
     * By default, the limit shall return the default value.
     *
     * @return void
     */
    public function testDefaultLimit()
    {
        $parameters = [];

        $parser = QueryParser::make($parameters);

        static::assertEquals(QueryParser::DEFAULT_LIMIT, $parser->getLimit());
    }

    /**
     * If the limit is less than the default, the parser shall return the limit.
     *
     * @return void
     */
    public function testValidLimit()
    {
        $limit = $this->faker->numberBetween(1, QueryParser::DEFAULT_LIMIT);

        $parameters = [
            QueryParser::PARAM_LIMIT => $limit,
        ];

        $parser = QueryParser::make($parameters);

        static::assertEquals($limit, $parser->getLimit());
    }

    /**
     * If the limit is greater than the default, the parser shall return the default.
     *
     * @return void
     */
    public function testInvalidLimit()
    {
        $limit = QueryParser::DEFAULT_LIMIT + $this->faker->randomDigitNotNull;

        $parameters = [
            QueryParser::PARAM_LIMIT => $limit,
        ];

        $parser = QueryParser::make($parameters);

        static::assertEquals(QueryParser::DEFAULT_LIMIT, $parser->getLimit());
    }

    /**
     * If the limit is lte to 0, the parser shall return the default limit.
     *
     * @return void
     */
    public function testPositiveBoundDefaultLimit()
    {
        $limit = $this->faker->randomDigit * -1;

        $parameters = [
            QueryParser::PARAM_LIMIT => $limit,
        ];

        $parser = QueryParser::make($parameters);

        static::assertEquals(QueryParser::DEFAULT_LIMIT, $parser->getLimit());
    }

    /**
     * If the default limit is overriden, the parser shall return the overriden value by default.
     *
     * @return void
     */
    public function testOverridenDefaultLimit()
    {
        $defaultLimit = $this->faker->randomDigitNotNull;

        $parameters = [];

        $parser = QueryParser::make($parameters);

        static::assertEquals($defaultLimit, $parser->getLimit($defaultLimit));
    }

    /**
     * If the default limit is overriden and the limit is valid, the parser shall return the limit.
     *
     * @return void
     */
    public function testValidOverridenDefaultLimit()
    {
        $limit = $this->faker->randomDigitNotNull;
        $defaultLimit = $limit + $this->faker->randomDigitNotNull;

        $parameters = [
            QueryParser::PARAM_LIMIT => $limit,
        ];

        $parser = QueryParser::make($parameters);

        static::assertEquals($limit, $parser->getLimit($defaultLimit));
    }

    /**
     * If the default limit is overriden and the limit is invalid, the parser shall return the default limit.
     *
     * @return void
     */
    public function testInvalidOverridenDefaultLimit()
    {
        $defaultLimit = $this->faker->randomDigitNotNull;
        $limit = $defaultLimit + $this->faker->randomDigitNotNull;

        $parameters = [
            QueryParser::PARAM_LIMIT => $limit,
        ];

        $parser = QueryParser::make($parameters);

        static::assertEquals($defaultLimit, $parser->getLimit($defaultLimit));
    }

    /**
     * If the default limit is overriden and the limit is lte to 0, the parser shall return the default limit.
     *
     * @return void
     */
    public function testPositiveBoundOverridenDefaultLimit()
    {
        $defaultLimit = $this->faker->randomDigitNotNull;
        $limit = $this->faker->randomDigit * -1;

        $parameters = [
            QueryParser::PARAM_LIMIT => $limit,
        ];

        $parser = QueryParser::make($parameters);

        static::assertEquals($defaultLimit, $parser->getLimit($defaultLimit));
    }
}
