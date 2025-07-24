<?php

declare(strict_types=1);

namespace Tests\Unit\Scout\Elasticsearch\Api\Parser;

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Sort\FieldCriteria as BaseFieldCriteria;
use App\Http\Api\Criteria\Sort\RandomCriteria;
use App\Http\Api\Criteria\Sort\RelationCriteria as BaseRelationCriteria;
use App\Http\Api\Scope\GlobalScope;
use App\Scout\Elasticsearch\Api\Criteria\Sort\FieldCriteria;
use App\Scout\Elasticsearch\Api\Criteria\Sort\RelationCriteria;
use App\Scout\Elasticsearch\Api\Parser\SortParser;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class SortParserTest extends TestCase
{
    use WithFaker;

    /**
     * The Sort Parser shall parse Relation Criteria.
     */
    public function testRelationCriteria(): void
    {
        $direction = Arr::random(Direction::cases());

        $criteria = new BaseRelationCriteria(new GlobalScope(), $this->faker->word(), $direction);

        static::assertInstanceOf(RelationCriteria::class, SortParser::parse($criteria));
    }

    /**
     * The Sort Parser shall parse Field Criteria.
     */
    public function testFieldCriteria(): void
    {
        $direction = Arr::random(Direction::cases());

        $criteria = new BaseFieldCriteria(new GlobalScope(), $this->faker->word(), $direction);

        static::assertInstanceOf(FieldCriteria::class, SortParser::parse($criteria));
    }

    /**
     * The Sort Parser shall not parse Random Criteria.
     */
    public function testRandomCriteria(): void
    {
        $criteria = new RandomCriteria(new GlobalScope());

        static::assertNull(SortParser::parse($criteria));
    }
}
