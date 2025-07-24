<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Query;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Criteria\Field\Criteria as FieldCriteria;
use App\Http\Api\Criteria\Include\Criteria as IncludeCriteria;
use App\Http\Api\Criteria\Include\ResourceCriteria;
use App\Http\Api\Criteria\Paging\LimitCriteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Criteria\Search\Criteria as SearchCriteria;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\SearchParser;
use App\Http\Api\Parser\SortParser;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Tests\TestCase;

class QueryTest extends TestCase
{
    use WithFaker;

    /**
     * The Query shall retrieve field criteria by type.
     */
    public function testGetFieldCriteria(): void
    {
        $type = $this->faker->word();

        $parameters = [
            FieldParser::param() => [
                $type => $this->faker->word(),
            ],
        ];

        $query = new FakeQuery($parameters);

        static::assertInstanceOf(FieldCriteria::class, $query->getFieldCriteria($type));
    }

    /**
     * The Query shall retrieve include criteria by type.
     */
    public function testGetIncludeCriteria(): void
    {
        $parameters = [
            IncludeParser::param() => $this->faker->word(),
        ];

        $query = new FakeQuery($parameters);

        static::assertInstanceOf(IncludeCriteria::class, $query->getIncludeCriteria($this->faker->word()));
    }

    /**
     * The Query shall retrieve include resource criteria by type.
     */
    public function testGetIncludeResourceCriteria(): void
    {
        $type = $this->faker->word();

        $parameters = [
            IncludeParser::param() => [
                $type => $this->faker->word(),
            ],
        ];

        $query = new FakeQuery($parameters);

        static::assertInstanceOf(ResourceCriteria::class, $query->getIncludeCriteria($type));
    }

    /**
     * The query shall retrieve all sort criteria.
     */
    public function testGetSortCriteria(): void
    {
        $fields = collect($this->faker()->words($this->faker->randomDigitNotNull()));

        $parameters = [
            SortParser::param() => $fields->join(','),
        ];

        $query = new FakeQuery($parameters);

        static::assertCount($fields->count(), $query->getSortCriteria());
    }

    /**
     * The query shall retrieve all filter criteria.
     */
    public function testGetFilterCriteria(): void
    {
        $filterCount = $this->faker->randomDigitNotNull();

        $parameters = Collection::times($filterCount, fn () => FilterParser::param().'.'.Str::random())
            ->combine(Collection::times($filterCount, fn () => Str::random()))
            ->undot()
            ->all();

        $query = new FakeQuery($parameters);

        static::assertCount($filterCount, $query->getFilterCriteria());
    }

    /**
     * By default, the query shall not have a search term.
     */
    public function testDoesNotHaveSearch(): void
    {
        $parameters = [];

        $query = new FakeQuery($parameters);

        static::assertFalse($query->hasSearchCriteria());
    }

    /**
     * The query shall have a search if a term is provided.
     */
    public function testHasSearch(): void
    {
        $parameters = [
            SearchParser::param() => $this->faker->word(),
        ];

        $query = new FakeQuery($parameters);

        static::assertTrue($query->hasSearchCriteria());
    }

    /**
     * By default, the query shall return null search criteria.
     */
    public function testNullSearch(): void
    {
        $parameters = [];

        $query = new FakeQuery($parameters);

        static::assertNull($query->getSearchCriteria());
    }

    /**
     * The query shall return search criteria if a term is provided.
     */
    public function testGetSearch(): void
    {
        $parameters = [
            SearchParser::param() => $this->faker->word(),
        ];

        $query = new FakeQuery($parameters);

        static::assertInstanceOf(SearchCriteria::class, $query->getSearchCriteria());
    }

    /**
     * The query shall return limit criteria.
     */
    public function testGetLimitCriteria(): void
    {
        $parameters = [];

        $query = new FakeQuery($parameters);

        static::assertInstanceOf(LimitCriteria::class, $query->getPagingCriteria(PaginationStrategy::LIMIT));
    }

    /**
     * The query shall return limit criteria.
     */
    public function testGetOffsetCriteria(): void
    {
        $parameters = [];

        $query = new FakeQuery($parameters);

        static::assertInstanceOf(OffsetCriteria::class, $query->getPagingCriteria(PaginationStrategy::OFFSET));
    }
}
