<?php

declare(strict_types=1);

namespace App\Http\Api\Filter;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Criteria\Filter\HasCriteria;
use App\Http\Api\Include\AllowedInclude;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

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
    protected readonly Collection $allowedIncludePaths;

    /**
     * Create a new filter instance.
     *
     * @param  AllowedInclude[]  $allowedIncludePaths
     */
    public function __construct(array $allowedIncludePaths)
    {
        parent::__construct(HasCriteria::PARAM_VALUE, HasCriteria::PARAM_VALUE);

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
                    return $this->allowedIncludePaths->contains(
                        fn (AllowedInclude $allowedInclude) => $allowedInclude->path() === $filterValue
                    );
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
        $paths = $this->allowedIncludePaths->map(fn (AllowedInclude $allowedInclude) => $allowedInclude->path());

        return [
            Rule::in($paths),
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
            ComparisonOperator::EQ(),
            ComparisonOperator::NE(),
            ComparisonOperator::LT(),
            ComparisonOperator::GT(),
            ComparisonOperator::LTE(),
            ComparisonOperator::GTE(),
        ];
    }
}
