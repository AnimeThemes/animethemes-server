<?php

declare(strict_types=1);

namespace App\Http\Api\Query;

use App\Contracts\Http\Api\Query\Query;
use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Criteria\Field\Criteria as FieldCriteria;
use App\Http\Api\Criteria\Filter\Criteria as FilterCriteria;
use App\Http\Api\Criteria\Include\Criteria as IncludeCriteria;
use App\Http\Api\Criteria\Include\ResourceCriteria;
use App\Http\Api\Criteria\Paging\Criteria as PagingCriteria;
use App\Http\Api\Criteria\Search\Criteria as SearchCriteria;
use App\Http\Api\Criteria\Sort\Criteria as SortCriteria;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SearchParser;
use App\Http\Api\Parser\SortParser;
use Illuminate\Support\Arr;

/**
 * Class ReadQuery.
 */
abstract class ReadQuery implements Query
{
    /**
     * The list of sparse fieldset criteria to apply to the query.
     *
     * @var FieldCriteria[]
     */
    protected readonly array $fieldCriteria;

    /**
     * The list of include paths per type.
     *
     * @var IncludeCriteria[]
     */
    protected readonly array $includeCriteria;

    /**
     * The list of sort criteria to apply to the query.
     *
     * @var SortCriteria[]
     */
    protected readonly array $sortCriteria;

    /**
     * The list of filter criteria to apply to the query.
     *
     * @var FilterCriteria[]
     */
    protected readonly array $filterCriteria;

    /**
     * The list of search criteria to apply to the query.
     *
     * @var SearchCriteria[]
     */
    protected readonly array $searchCriteria;

    /**
     * The list of paging criteria to apply to the query.
     *
     * @var PagingCriteria[]
     */
    protected readonly array $pagingCriteria;

    /**
     * Create a new query instance.
     *
     * @param  array  $parameters
     */
    public function __construct(array $parameters = [])
    {
        $this->fieldCriteria = FieldParser::parse($parameters);
        $this->includeCriteria = IncludeParser::parse($parameters);
        $this->sortCriteria = SortParser::parse($parameters);
        $this->filterCriteria = FilterParser::parse($parameters);
        $this->searchCriteria = SearchParser::parse($parameters);
        $this->pagingCriteria = PagingParser::parse($parameters);
    }

    /**
     * Get the field criteria.
     *
     * @param  string  $type
     * @return FieldCriteria|null
     */
    public function getFieldCriteria(string $type): ?FieldCriteria
    {
        return Arr::first($this->fieldCriteria, fn (FieldCriteria $criteria) => $criteria->getType() === $type);
    }

    /**
     * Get include criteria.
     *
     * @param  string  $type
     * @return IncludeCriteria|null
     */
    public function getIncludeCriteria(string $type): ?IncludeCriteria
    {
        return $this->getResourceIncludeCriteria($type) ??
            Arr::first(
                $this->includeCriteria,
                fn (IncludeCriteria $criteria) => get_class($criteria) === IncludeCriteria::class
            );
    }

    /**
     * Get the resource include criteria.
     *
     * @param  string  $type
     * @return IncludeCriteria|null
     */
    public function getResourceIncludeCriteria(string $type): ?IncludeCriteria
    {
        return Arr::first(
            $this->includeCriteria,
            fn (IncludeCriteria $criteria) => $criteria instanceof ResourceCriteria && $criteria->getType() === $type
        );
    }

    /**
     * Get sort criteria.
     *
     * @return SortCriteria[]
     */
    public function getSortCriteria(): array
    {
        return $this->sortCriteria;
    }

    /**
     * Get filter criteria for the field.
     *
     * @return FilterCriteria[]
     */
    public function getFilterCriteria(): array
    {
        return $this->filterCriteria;
    }

    /**
     * Does the query have search criteria?
     *
     * @return bool
     */
    public function hasSearchCriteria(): bool
    {
        return ! empty($this->searchCriteria);
    }

    /**
     * Get search criteria.
     * Note: At the time of writing, the API shall only support a single search term.
     *
     * @return SearchCriteria|null
     */
    public function getSearchCriteria(): ?SearchCriteria
    {
        return Arr::first($this->searchCriteria);
    }

    /**
     * Get paging criteria that matches pagination strategy.
     *
     * @param  PaginationStrategy  $strategy
     * @return PagingCriteria|null
     */
    public function getPagingCriteria(PaginationStrategy $strategy): ?PagingCriteria
    {
        return Arr::first(
            $this->pagingCriteria,
            fn (PagingCriteria $criteria) => $strategy->is($criteria->getStrategy())
        );
    }
}
