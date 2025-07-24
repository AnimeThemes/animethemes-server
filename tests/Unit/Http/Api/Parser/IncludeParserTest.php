<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Parser;

use App\Http\Api\Criteria\Include\Criteria;
use App\Http\Api\Criteria\Include\ResourceCriteria;
use App\Http\Api\Parser\IncludeParser;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IncludeParserTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Field Parser shall return no criteria.
     */
    public function testNoCriteriaByDefault(): void
    {
        $parameters = [];

        static::assertEmpty(IncludeParser::parse($parameters));
    }

    /**
     * The Include Parser shall parse Include Criteria.
     */
    public function testParseCriteria(): void
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
     */
    public function testParseCriteriaPaths(): void
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
     */
    public function testParseResourceCriteria(): void
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
     */
    public function testParseResourceCriteriaType(): void
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
     */
    public function testParseResourceCriteriaPaths(): void
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
