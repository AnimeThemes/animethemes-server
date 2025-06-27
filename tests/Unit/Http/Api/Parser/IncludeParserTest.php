<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Parser;

use App\Http\Api\Criteria\Include\Criteria;
use App\Http\Api\Criteria\Include\ResourceCriteria;
use App\Http\Api\Parser\IncludeParser;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class IncludeParserTest.
 */
class IncludeParserTest extends TestCase
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

        static::assertEmpty(IncludeParser::parse($parameters));
    }

    /**
     * The Include Parser shall parse Include Criteria.
     *
     * @return void
     */
    public function test_parse_criteria(): void
    {
        $fields = collect($this->faker()->words($this->faker->randomDigitNotNull()));

        $parameters = [
            IncludeParser::param() => $fields->join(','),
        ];

        $criteria = IncludeParser::parse($parameters)[0];

        static::assertInstanceOf(Criteria::class, $criteria);
    }

    /**
     * The Include Parser shall parse Include Criteria paths.
     *
     * @return void
     */
    public function test_parse_criteria_paths(): void
    {
        $fields = $this->faker()->words($this->faker->randomDigitNotNull());

        $parameters = [
            IncludeParser::param() => collect($fields)->join(','),
        ];

        $criteria = IncludeParser::parse($parameters)[0];

        static::assertEquals($fields, $criteria->getPaths()->all());
    }

    /**
     * The Include Parser shall parse Resource Criteria.
     *
     * @return void
     */
    public function test_parse_resource_criteria(): void
    {
        $fields = collect($this->faker()->words($this->faker->randomDigitNotNull()));

        $parameters = [
            IncludeParser::param() => [
                $this->faker->word() => $fields->join(','),
            ],
        ];

        $criteria = IncludeParser::parse($parameters)[0];

        static::assertInstanceOf(ResourceCriteria::class, $criteria);
    }

    /**
     * The Include Parser shall parse Resource Criteria type.
     *
     * @return void
     */
    public function test_parse_resource_criteria_type(): void
    {
        $type = $this->faker->word();

        $fields = collect($this->faker()->words($this->faker->randomDigitNotNull()));

        $parameters = [
            IncludeParser::param() => [
                $type => $fields->join(','),
            ],
        ];

        $criteria = IncludeParser::parse($parameters)[0];

        static::assertTrue(
            $criteria instanceof ResourceCriteria
            && $criteria->getType() === $type
        );
    }

    /**
     * The Include Parser shall parse Include Resource Criteria paths.
     *
     * @return void
     */
    public function test_parse_resource_criteria_paths(): void
    {
        $fields = $this->faker()->words($this->faker->randomDigitNotNull());

        $parameters = [
            IncludeParser::param() => [
                $this->faker->word() => collect($fields)->join(','),
            ],
        ];

        $criteria = IncludeParser::parse($parameters)[0];

        static::assertEquals($fields, $criteria->getPaths()->all());
    }
}
