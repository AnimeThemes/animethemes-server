<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Elasticsearch\Api\Parser;

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Sort\FieldCriteria as BaseFieldCriteria;
use App\Http\Api\Criteria\Sort\RandomCriteria;
use App\Http\Api\Criteria\Sort\RelationCriteria as BaseRelationCriteria;
use App\Services\Elasticsearch\Api\Criteria\Sort\FieldCriteria;
use App\Services\Elasticsearch\Api\Criteria\Sort\RelationCriteria;
use App\Services\Elasticsearch\Api\Parser\SortParser;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class SortParserTest.
 */
class SortParserTest extends TestCase
{
    use WithFaker;

    /**
     * The Sort Parser shall parse Relation Criteria.
     *
     * @return void
     */
    public function testRelationCriteria(): void
    {
        $criteria = new BaseRelationCriteria($this->faker->word(), Direction::getRandomInstance());

        static::assertInstanceOf(RelationCriteria::class, SortParser::parse($criteria));
    }

    /**
     * The Sort Parser shall parse Field Criteria.
     *
     * @return void
     */
    public function testFieldCriteria(): void
    {
        $criteria = new BaseFieldCriteria($this->faker->word(), Direction::getRandomInstance());

        static::assertInstanceOf(FieldCriteria::class, SortParser::parse($criteria));
    }

    /**
     * The Sort Parser shall not parse Random Criteria.
     *
     * @return void
     */
    public function testRandomCriteria(): void
    {
        $criteria = new RandomCriteria();

        static::assertNull(SortParser::parse($criteria));
    }
}
