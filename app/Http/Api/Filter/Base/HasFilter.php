<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Base;

use App\Http\Api\Criteria\Filter\HasCriteria;
use App\Http\Api\Filter\Filter;
use Illuminate\Support\Collection;

/**
 * Class HasFilter.
 */
class HasFilter extends Filter
{
    /**
     * The list of allowed include paths that the filter can be applied to.
     *
     * @var Collection
     */
    protected Collection $allowedIncludePaths;

    /**
     * Create a new filter instance.
     *
     * @param  Collection  $criteria
     * @param  array  $allowedIncludePaths
     */
    public function __construct(Collection $criteria, array $allowedIncludePaths)
    {
        parent::__construct($criteria, HasCriteria::PARAM_VALUE);

        $this->allowedIncludePaths = collect($allowedIncludePaths);
    }

    /**
     * Convert filter values to integers.
     *
     * @param  array  $filterValues
     * @return array
     */
    protected function convertFilterValues(array $filterValues): array
    {
        return $filterValues;
    }

    /**
     * Get only filter values that are strings.
     *
     * @param  array  $filterValues
     * @return array
     */
    protected function getValidFilterValues(array $filterValues): array
    {
        return array_values(
            array_filter(
                $filterValues,
                function (string $filterValue) {
                    return $this->allowedIncludePaths->contains($filterValue);
                }
            )
        );
    }

    /**
     * Determine if all valid filter values have been specified.
     * By default, this is false as we assume an unrestricted amount of valid values.
     *
     * @param  array  $filterValues
     * @return bool
     */
    protected function isAllFilterValues(array $filterValues): bool
    {
        return false;
    }
}
