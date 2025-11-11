<?php

declare(strict_types=1);

namespace App\Http\Api\Query;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Criteria\Field\Criteria as FieldCriteria;
use App\Http\Api\Criteria\Filter\Criteria as FilterCriteria;
use App\Http\Api\Criteria\Include\Criteria as IncludeCriteria;
use App\Http\Api\Criteria\Include\ResourceCriteria;
use App\Http\Api\Criteria\Paging\Criteria as PagingCriteria;
use App\Http\Api\Criteria\Sort\Criteria as SortCriteria;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SearchParser;
use App\Http\Api\Parser\SortParser;
use App\Search\Criteria as SearchCriteria;
use Illuminate\Support\Arr;

class Query
{
    /**
     * The list of sparse fieldset criteria to apply to the query.
     *
     * @var FieldCriteria[]
     */
    protected array $fieldCriteria;

    /**
     * The list of include paths per type.
     *
     * @var IncludeCriteria[]
     */
    protected array $includeCriteria;

    /**
     * The list of sort criteria to apply to the query.
     *
     * @var SortCriteria[]
     */
    protected array $sortCriteria;

    /**
     * The list of filter criteria to apply to the query.
     *
     * @var FilterCriteria[]
     */
    protected array $filterCriteria;

    /**
     * The list of search criteria to apply to the query.
     *
     * @var SearchCriteria[]
     */
    protected array $searchCriteria;

    /**
     * The list of paging criteria to apply to the query.
     *
     * @var PagingCriteria[]
     */
    protected array $pagingCriteria;

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
     */
    public function getFieldCriteria(string $type): ?FieldCriteria
    {
        return Arr::first($this->fieldCriteria, fn (FieldCriteria $criteria): bool => $criteria->getType() === $type);
    }

    /**
     * Get include criteria.
     */
    public function getIncludeCriteria(string $type): ?IncludeCriteria
    {
        return $this->getResourceIncludeCriteria($type) ??
            Arr::first(
                $this->includeCriteria,
                fn (IncludeCriteria $criteria): bool => $criteria::class === IncludeCriteria::class
            );
    }

    /**
     * Get the resource include criteria.
     */
    public function getResourceIncludeCriteria(string $type): ?IncludeCriteria
    {
        return Arr::first(
            $this->includeCriteria,
            fn (IncludeCriteria $criteria): bool => $criteria instanceof ResourceCriteria && $criteria->getType() === $type
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
     */
    public function hasSearchCriteria(): bool
    {
        return $this->searchCriteria !== [];
    }

    /**
     * Get search criteria.
     * Note: At the time of writing, the API shall only support a single search term.
     */
    public function getSearchCriteria(): ?SearchCriteria
    {
        return Arr::first($this->searchCriteria);
    }

    /**
     * Get paging criteria that matches pagination strategy.
     */
    public function getPagingCriteria(PaginationStrategy $strategy): ?PagingCriteria
    {
        return Arr::first(
            $this->pagingCriteria,
            fn (PagingCriteria $criteria): bool => $strategy === $criteria->getStrategy()
        );
    }
}
