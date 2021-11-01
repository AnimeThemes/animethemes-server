<?php

declare(strict_types=1);

namespace App\Http\Api;

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
use Illuminate\Support\Collection;

/**
 * Class Query.
 */
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

    /**
     * The list of parameters that the parser reads from.
     *
     * @return string[]
     */
    public static function parameters(): array
    {
        return [
            FieldParser::$param,
            IncludeParser::$param,
            SortParser::$param,
            FilterParser::$param,
            SearchParser::$param,
            PagingParser::$param,
        ];
    }

    /**
     * Create a new query parser instance.
     *
     * @param  array  $parameters
     */
    final public function __construct(array $parameters = [])
    {
        $this->fieldCriteria = FieldParser::parse($parameters);
        $this->includeCriteria = IncludeParser::parse($parameters);
        $this->sortCriteria = SortParser::parse($parameters);
        $this->filterCriteria = FilterParser::parse($parameters);
        $this->searchCriteria = SearchParser::parse($parameters);
        $this->pagingCriteria = PagingParser::parse($parameters);
    }

    /**
     * Create a new query parser instance.
     *
     * @param  mixed  ...$parameters
     * @return static
     */
    public static function make(...$parameters): static
    {
        return new static(...$parameters);
    }

    /**
     * Get the field criteria.
     *
     * @param  string  $type
     * @return FieldCriteria|null
     */
    public function getFieldCriteria(string $type): ?FieldCriteria
    {
        return collect($this->fieldCriteria)->first(fn (FieldCriteria $criteria) => $criteria->getType() === $type);
    }

    /**
     * Get the include criteria.
     *
     * @param  string  $type
     * @return IncludeCriteria|null
     */
    public function getIncludeCriteria(string $type): ?IncludeCriteria
    {
        return $this->getResourceIncludeCriteria($type) ??
            collect($this->includeCriteria)->first(
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
        return collect($this->includeCriteria)->first(
            fn (IncludeCriteria $criteria) => $criteria instanceof ResourceCriteria && $criteria->getType() === $type
        );
    }

    /**
     * Get sort criteria.
     *
     * @return Collection
     */
    public function getSortCriteria(): Collection
    {
        return collect($this->sortCriteria);
    }

    /**
     * Get filter criteria for the field.
     *
     * @return Collection
     */
    public function getFilterCriteria(): Collection
    {
        return collect($this->filterCriteria);
    }

    /**
     * Does the query have search criteria?
     *
     * @return bool
     */
    public function hasSearchCriteria(): bool
    {
        return collect($this->searchCriteria)->isNotEmpty();
    }

    /**
     * Get search criteria.
     * Note: At the time of writing, the API shall only support a single search term.
     *
     * @return SearchCriteria|null
     */
    public function getSearchCriteria(): ?SearchCriteria
    {
        return collect($this->searchCriteria)->first();
    }

    /**
     * Get paging criteria that matches pagination strategy.
     *
     * @param  PaginationStrategy  $strategy
     * @return PagingCriteria|null
     */
    public function getPagingCriteria(PaginationStrategy $strategy): ?PagingCriteria
    {
        return collect($this->pagingCriteria)->first(
            fn (PagingCriteria $criteria) => $strategy->is($criteria->getStrategy())
        );
    }
}
