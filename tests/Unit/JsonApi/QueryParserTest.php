<?php

namespace Tests\Unit\JsonApi;

use App\JsonApi\QueryParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class QueryParserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Fields shall be allowed if included in the sparse fieldsets for its type.
     *
     * @return void
     */
    public function testFieldsAreAllowed()
    {
        $type = $this->faker->word();
        $fields = $this->faker->words();

        $parameters = [
            'fields' => [
                $type => implode(',', $fields),
            ],
        ];

        $parser = new QueryParser($parameters);

        $this->assertTrue($parser->isAllowedField($type, collect($fields)->random()));
    }
}
