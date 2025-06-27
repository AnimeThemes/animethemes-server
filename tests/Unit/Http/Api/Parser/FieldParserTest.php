<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Parser;

use App\Http\Api\Criteria\Field\Criteria;
use App\Http\Api\Parser\FieldParser;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class FieldParserTest.
 */
class FieldParserTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Field Parser shall return no criteria.
     *
     * @return void
     */
    public function test_no_criteria_by_default(): void
    {
        $parameters = [];

        static::assertEmpty(FieldParser::parse($parameters));
    }

    /**
     * The Field Parser shall parse criteria defined in the field param.
     *
     * @return void
     */
    public function test_parse_criteria(): void
    {
        $fields = collect($this->faker()->words($this->faker->randomDigitNotNull()));

        $parameters = [
            FieldParser::param() => [
                $this->faker->word() => $fields->join(','),
            ],
        ];

        $criteria = FieldParser::parse($parameters)[0];

        static::assertInstanceOf(Criteria::class, $criteria);
    }

    /**
     * The Field Parser shall parse criteria type.
     *
     * @return void
     */
    public function test_parse_type(): void
    {
        $type = $this->faker->word();

        $fields = collect($this->faker()->words($this->faker->randomDigitNotNull()));

        $parameters = [
            FieldParser::param() => [
                $type => $fields->join(','),
            ],
        ];

        $criteria = FieldParser::parse($parameters)[0];

        static::assertEquals($type, $criteria->getType());
    }

    /**
     * The Field Parser shall parse criteria fields.
     *
     * @return void
     */
    public function test_parse_fields(): void
    {
        $fields = $this->faker()->words($this->faker->randomDigitNotNull());

        $parameters = [
            FieldParser::param() => [
                $this->faker->word() => collect($fields)->join(','),
            ],
        ];

        $criteria = FieldParser::parse($parameters)[0];

        static::assertEquals($fields, $criteria->getFields()->all());
    }
}
