<?php

declare(strict_types=1);

namespace App\Http\Api\Filter;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Criteria\Filter\HasCriteria;
use App\Http\Api\Include\AllowedInclude;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class HasFilter extends Filter
{
    /**
     * Create a new filter instance.
     *
     * @param  AllowedInclude[]  $allowedIncludePaths
     */
    public function __construct(protected readonly array $allowedIncludePaths)
    {
        parent::__construct(HasCriteria::PARAM_VALUE, HasCriteria::PARAM_VALUE);
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
        $paths = Arr::map($this->allowedIncludePaths, fn (AllowedInclude $allowedInclude) => $allowedInclude->path());

        return array_intersect($filterValues, $paths);
    }

    /**
     * Determine if all valid filter values have been specified.
     * By default, this is false as we assume an unrestricted amount of valid values.
     *
     * @param  array  $filterValues
     * @return bool
     */
    public function isAllFilterValues(array $filterValues): bool
    {
        return false;
    }

    /**
     * Get the validation rules for the filter.
     *
     * @return array
     */
    public function getRules(): array
    {
        $paths = Arr::map($this->allowedIncludePaths, fn (AllowedInclude $allowedInclude) => $allowedInclude->path());

        return [
            'required',
            Rule::in($paths)->__toString(),
        ];
    }

    /**
     * Get the allowed comparison operators for the filter.
     *
     * @return ComparisonOperator[]
     */
    public function getAllowedComparisonOperators(): array
    {
        return [
            ComparisonOperator::EQ,
            ComparisonOperator::NE,
            ComparisonOperator::LT,
            ComparisonOperator::GT,
            ComparisonOperator::LTE,
            ComparisonOperator::GTE,
        ];
    }
}
