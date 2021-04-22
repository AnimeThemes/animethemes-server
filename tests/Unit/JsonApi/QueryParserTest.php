<?php

namespace Tests\Unit\JsonApi;

use App\JsonApi\QueryParser;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

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

        $parser = new QueryParser($parameters);

        foreach ($fields as $field) {
            $this->assertTrue($parser->isAllowedField($type, $field));
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

        $parser = new QueryParser($parameters);

        foreach ($fields as $field) {
            $this->assertFalse($parser->isAllowedField($type, $field));
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
        $allowed_field = Arr::random($fields);

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                $type => $allowed_field,
            ],
        ];

        $parser = new QueryParser($parameters);

        foreach ($fields as $field) {
            if ($field == $allowed_field) {
                $this->assertTrue($parser->isAllowedField($type, $field));
            } else {
                $this->assertFalse($parser->isAllowedField($type, $field));
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

        $parser = new QueryParser($parameters);

        $this->assertEmpty($parser->getIncludePaths($includes));
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

        $parser = new QueryParser($parameters);

        $this->assertEmpty($parser->getIncludePaths($includes));
    }

    /**
     * If include paths are specified, those within the allowed include list are kept.
     *
     * @return void
     */
    public function testIncludeAllowedPaths()
    {
        $include_count = $this->faker->randomDigitNotNull;
        $includes = $this->faker->words($include_count * 2);
        $allowed_includes = Arr::random($includes, $include_count);

        $parameters = [
            QueryParser::PARAM_INCLUDE => implode(',', $includes),
        ];

        $parser = new QueryParser($parameters);

        $this->assertEmpty(array_diff($allowed_includes, $parser->getIncludePaths($allowed_includes)));
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

        $parser = new QueryParser($parameters);

        $this->assertEmpty($parser->getResourceIncludePaths($includes, $type));
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

        $parser = new QueryParser($parameters);

        $this->assertEmpty($parser->getResourceIncludePaths($includes, $type));
    }

    /**
     * If resource include paths are specified, those within the allowed include list for the resource are kept.
     *
     * @return void
     */
    public function testResourceIncludeAllowedPaths()
    {
        $type = $this->faker->word();
        $include_count = $this->faker->randomDigitNotNull;
        $includes = $this->faker->words($include_count * 2);
        $allowed_includes = Arr::random($includes, $include_count);

        $parameters = [
            QueryParser::PARAM_INCLUDE => [
                $type => implode(',', $includes),
            ],
        ];

        $parser = new QueryParser($parameters);

        $this->assertEmpty(array_diff($allowed_includes, $parser->getResourceIncludePaths($allowed_includes, $type)));
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

        $parser = new QueryParser($parameters);
        $parser_sorts = $parser->getSorts();

        foreach ($sorts as $sort) {
            $this->assertTrue(Arr::get($parser_sorts, $sort));
        }
    }

    /**
     * Sorts that are not prefixed with '-' are parsed as descending.
     *
     * @return void
     */
    public function testDescSortsAreParsed()
    {
        $sorts_desc = $this->faker->unique()->words($this->faker->randomDigitNotNull);
        $sorts_desc_symbol = array_map(function (string $sort) {
            return '-'.$sort;
        }, $sorts_desc);

        $parameters = [
            QueryParser::PARAM_SORT => implode(',', $sorts_desc_symbol),
        ];

        $parser = new QueryParser($parameters);
        $parser_sorts = $parser->getSorts();

        foreach ($sorts_desc as $sort) {
            $this->assertFalse(Arr::get($parser_sorts, $sort));
        }
    }

    /**
     * Sorts that are not prefixed with '+' are parsed as ascending.
     *
     * @return void
     */
    public function testAscSortsAreParsed()
    {
        $sorts_asc = $this->faker->unique()->words($this->faker->randomDigitNotNull);
        $sorts_asc_symbol = array_map(function (string $sort) {
            return '+'.$sort;
        }, $sorts_asc);

        $parameters = [
            QueryParser::PARAM_SORT => implode(',', $sorts_asc_symbol),
        ];

        $parser = new QueryParser($parameters);
        $parser_sorts = $parser->getSorts();

        foreach ($sorts_asc as $sort) {
            $this->assertTrue(Arr::get($parser_sorts, $sort));
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
        $filter_field = Arr::random($fields);
        $filter = $this->faker->word();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filter_field => $filter,
            ],
        ];

        $parser = new QueryParser($parameters);

        foreach ($fields as $field) {
            if ($field == $filter_field) {
                $this->assertTrue($parser->hasCondition($field));
            } else {
                $this->assertFalse($parser->hasCondition($field));
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
        $filter_field = Arr::random($fields);
        $filter = $this->faker->word();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filter_field => $filter,
            ],
        ];

        $parser = new QueryParser($parameters);

        foreach ($fields as $field) {
            if ($field == $filter_field) {
                $this->assertNotEmpty($parser->getConditions($field));
            } else {
                $this->assertEmpty($parser->getConditions($field));
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

        $parser = new QueryParser($parameters);

        $this->assertFalse($parser->hasSearch());
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

        $parser = new QueryParser($parameters);

        $this->assertTrue($parser->hasSearch());
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

        $parser = new QueryParser($parameters);

        $this->assertEquals($q, $parser->getSearch());
    }

    /**
     * By default, the limit shall return the default value.
     *
     * @return void
     */
    public function testDefaultLimit()
    {
        $parameters = [];

        $parser = new QueryParser($parameters);

        $this->assertEquals(QueryParser::DEFAULT_LIMIT, $parser->getLimit());
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

        $parser = new QueryParser($parameters);

        $this->assertEquals($limit, $parser->getLimit());
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

        $parser = new QueryParser($parameters);

        $this->assertEquals(QueryParser::DEFAULT_LIMIT, $parser->getLimit());
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

        $parser = new QueryParser($parameters);

        $this->assertEquals(QueryParser::DEFAULT_LIMIT, $parser->getLimit());
    }

    /**
     * If the default limit is overriden, the parser shall return the overriden value by default.
     *
     * @return void
     */
    public function testOverridenDefaultLimit()
    {
        $default_limit = $this->faker->randomDigitNotNull;

        $parameters = [];

        $parser = new QueryParser($parameters);

        $this->assertEquals($default_limit, $parser->getLimit($default_limit));
    }

    /**
     * If the default limit is overriden and the limit is valid, the parser shall return the limit.
     *
     * @return void
     */
    public function testValidOverridenDefaultLimit()
    {
        $limit = $this->faker->randomDigitNotNull;
        $default_limit = $limit + $this->faker->randomDigitNotNull;

        $parameters = [
            QueryParser::PARAM_LIMIT => $limit,
        ];

        $parser = new QueryParser($parameters);

        $this->assertEquals($limit, $parser->getLimit($default_limit));
    }

    /**
     * If the default limit is overriden and the limit is invalid, the parser shall return the default limit.
     *
     * @return void
     */
    public function testInvalidOverridenDefaultLimit()
    {
        $default_limit = $this->faker->randomDigitNotNull;
        $limit = $default_limit + $this->faker->randomDigitNotNull;

        $parameters = [
            QueryParser::PARAM_LIMIT => $limit,
        ];

        $parser = new QueryParser($parameters);

        $this->assertEquals($default_limit, $parser->getLimit($default_limit));
    }

    /**
     * If the default limit is overriden and the limit is lte to 0, the parser shall return the default limit.
     *
     * @return void
     */
    public function testPositiveBoundOverridenDefaultLimit()
    {
        $default_limit = $this->faker->randomDigitNotNull;
        $limit = $this->faker->randomDigit * -1;

        $parameters = [
            QueryParser::PARAM_LIMIT => $limit,
        ];

        $parser = new QueryParser($parameters);

        $this->assertEquals($default_limit, $parser->getLimit($default_limit));
    }
}
