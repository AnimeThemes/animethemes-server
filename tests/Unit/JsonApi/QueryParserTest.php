<?php

namespace Tests\Unit\JsonApi;

use App\Enums\AnimeSeason;
use App\JsonApi\QueryParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class QueryParserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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
            'fields' => [
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
            'fields' => [
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
     * By default, all include paths are allowed if include paths are not specified.
     *
     * @return void
     */
    public function testAllAllowedIncludesByDefault()
    {
        $includes = $this->faker->words($this->faker->randomDigitNotNull);

        $parameters = [];

        $parser = new QueryParser($parameters);

        $this->assertEmpty(array_diff($includes, $parser->getIncludePaths($includes)));
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
            'include' => '',
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
            'include' => implode(',', $includes),
        ];

        $parser = new QueryParser($parameters);

        $this->assertEmpty(array_diff($allowed_includes, $parser->getIncludePaths($allowed_includes)));
    }

    /**
     * By default, all include paths are allowed if selected include paths are not specified.
     *
     * @return void
     */
    public function testAllAllowedResourceIncludesByDefault()
    {
        $type = $this->faker->word();
        $includes = $this->faker->words($this->faker->randomDigitNotNull);

        $parameters = [];

        $parser = new QueryParser($parameters);

        $this->assertEmpty(array_diff($includes, $parser->getResourceIncludePaths($includes, $type)));
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
            'include' => [
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
            'include' => [
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
            'sort' => implode(',', $sorts),
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
        $sorts_desc_symbol = array_map(function ($sort) {
            return '-'.$sort;
        }, $sorts_desc);

        $parameters = [
            'sort' => implode(',', $sorts_desc_symbol),
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
        $sorts_asc_symbol = array_map(function ($sort) {
            return '+'.$sort;
        }, $sorts_asc);

        $parameters = [
            'sort' => implode(',', $sorts_asc_symbol),
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
            'filter' => [
                $filter_field => $filter,
            ],
        ];

        $parser = new QueryParser($parameters);

        foreach ($fields as $field) {
            if ($field == $filter_field) {
                $this->assertTrue($parser->hasFilter($field));
            } else {
                $this->assertFalse($parser->hasFilter($field));
            }
        }
    }

    /**
     * The parser shall collect filter values for a field.
     *
     * @return void
     */
    public function testGetFilter()
    {
        $fields = $this->faker->words($this->faker->randomDigitNotNull);
        $filter_field = Arr::random($fields);
        $filter = $this->faker->word();

        $parameters = [
            'filter' => [
                $filter_field => $filter,
            ],
        ];

        $parser = new QueryParser($parameters);

        foreach ($fields as $field) {
            if ($field == $filter_field) {
                $this->assertEmpty(array_diff([$filter], $parser->getFilter($field)));
            } else {
                $this->assertEmpty($parser->getFilter($field));
            }
        }
    }

    /**
     * The parser shall convert enum filters from keys to values.
     *
     * @return void
     */
    public function testEnumFilter()
    {
        $enum = AnimeSeason::getRandomInstance();
        $filter_field = $this->faker->word();

        $parameters = [
            'filter' => [
                $filter_field => $enum->key,
            ],
        ];

        $parser = new QueryParser($parameters);

        $this->assertEmpty(array_diff([$enum->value], $parser->getEnumFilter($filter_field, AnimeSeason::class)));
    }

    /**
     * The parser shall convert boolean filters from strings to boolean values.
     *
     * @return void
     */
    public function testBooleanFilter()
    {
        $bool = $this->faker->boolean();
        $filter_field = $this->faker->word();

        $parameters = [
            'filter' => [
                $filter_field => json_encode($bool),
            ],
        ];

        $parser = new QueryParser($parameters);

        $this->assertEmpty(array_diff([$bool], $parser->getBooleanFilter($filter_field)));
    }

    /**
     * The parser will indicate that our request doesn't have a search value if not specified.
     *
     * @return void
     */
    public function testDoesNotHaveSearch()
    {
        $parameters = [
            'q' => '',
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
            'q' => $this->faker->word(),
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
            'q' => $q,
        ];

        $parser = new QueryParser($parameters);

        $this->assertEquals($q, $parser->getSearch());
    }
}
