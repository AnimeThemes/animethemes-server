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

/**
 * Class QueryTest.
 */
class QueryTest extends TestCase
{
    use WithFaker;

    /**
     * The Query shall retrieve field criteria by type.
     *
     * @return void
     */
    public function testGetFieldCriteria(): void
    {
        $type = $this->faker->word();

        $parameters = [
            FieldParser::param() => [
                $type => $this->faker->word(),
            ],
        ];

        $query = FakeQuery::make($parameters);

        static::assertInstanceOf(FieldCriteria::class, $query->getFieldCriteria($type));
    }

    /**
     * The Query shall retrieve include criteria by type.
     *
     * @return void
     */
    public function testGetIncludeCriteria(): void
    {
        $parameters = [
            IncludeParser::param() => $this->faker->word(),
        ];

        $query = FakeQuery::make($parameters);

        static::assertInstanceOf(IncludeCriteria::class, $query->getIncludeCriteria($this->faker->word()));
    }

    /**
     * The Query shall retrieve include resource criteria by type.
     *
     * @return void
     */
    public function testGetIncludeResourceCriteria(): void
    {
        $type = $this->faker->word();

        $parameters = [
            IncludeParser::param() => [
                $type => $this->faker->word(),
            ],
        ];

        $query = FakeQuery::make($parameters);

        static::assertInstanceOf(ResourceCriteria::class, $query->getIncludeCriteria($type));
    }

    /**
     * The query shall retrieve all sort criteria.
     *
     * @return void
     */
    public function testGetSortCriteria(): void
    {
        $fields = collect($this->faker()->words($this->faker->randomDigitNotNull()));

        $parameters = [
            SortParser::param() => $fields->join(','),
        ];

        $query = FakeQuery::make($parameters);

        static::assertEquals($fields->count(), $query->getSortCriteria()->count());
    }

    /**
     * The query shall retrieve all filter criteria.
     *
     * @return void
     */
    public function testGetFilterCriteria(): void
    {
        $filterCount = $this->faker->randomDigitNotNull();

        $parameters = Collection::times($filterCount, fn () => FilterParser::param().'.'.Str::random())
            ->combine(Collection::times($filterCount, fn () => Str::random()))
            ->undot()
            ->all();

        $query = FakeQuery::make($parameters);

        static::assertEquals($filterCount, $query->getFilterCriteria()->count());
    }

    /**
     * By default, the query shall not have a search term.
     *
     * @return void
     */
    public function testDoesNotHaveSearch(): void
    {
        $parameters = [];

        $query = FakeQuery::make($parameters);

        static::assertFalse($query->hasSearchCriteria());
    }

    /**
     * The query shall have a search if a term is provided.
     *
     * @return void
     */
    public function testHasSearch(): void
    {
        $parameters = [
            SearchParser::param() => $this->faker->word(),
        ];

        $query = FakeQuery::make($parameters);

        static::assertTrue($query->hasSearchCriteria());
    }

    /**
     * By default, the query shall return null search criteria.
     *
     * @return void
     */
    public function testNullSearch(): void
    {
        $parameters = [];

        $query = FakeQuery::make($parameters);

        static::assertNull($query->getSearchCriteria());
    }

    /**
     * The query shall return search criteria if a term is provided.
     *
     * @return void
     */
    public function testGetSearch(): void
    {
        $parameters = [
            SearchParser::param() => $this->faker->word(),
        ];

        $query = FakeQuery::make($parameters);

        static::assertInstanceOf(SearchCriteria::class, $query->getSearchCriteria());
    }

    /**
     * The query shall return limit criteria.
     *
     * @return void
     */
    public function testGetLimitCriteria(): void
    {
        $parameters = [];

        $query = FakeQuery::make($parameters);

        static::assertInstanceOf(LimitCriteria::class, $query->getPagingCriteria(PaginationStrategy::LIMIT()));
    }

    /**
     * The query shall return limit criteria.
     *
     * @return void
     */
    public function testGetOffsetCriteria(): void
    {
        $parameters = [];

        $query = FakeQuery::make($parameters);

        static::assertInstanceOf(OffsetCriteria::class, $query->getPagingCriteria(PaginationStrategy::OFFSET()));
    }
}
