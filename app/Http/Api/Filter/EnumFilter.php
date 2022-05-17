<?php

declare(strict_types=1);

namespace App\Http\Api\Filter;

use App\Enums\BaseEnum;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Rules\Api\EnumDescriptionRule;

/**
 * Class EnumFilter.
 */
class EnumFilter extends Filter
{
    /**
     * Create a new filter instance.
     *
     * @param  string  $key
     * @param  string|null  $column
     * @param  class-string<BaseEnum>  $enumClass
     */
    public function __construct(string $key, protected readonly string $enumClass, ?string $column = null)
    {
        parent::__construct($key, $column);
    }

    /**
     * Convert filter values to enum values from key.
     *
     * @param  array  $filterValues
     * @return array
     */
    protected function convertFilterValues(array $filterValues): array
    {
        return array_map(
            fn (string $filterValue) => $this->enumClass::fromDescription($filterValue)?->value,
            $filterValues
        );
    }

    /**
     * Get only filter values that are valid enum options.
     *
     * @param  array  $filterValues
     * @return array
     */
    protected function getValidFilterValues(array $filterValues): array
    {
        return array_values(
            array_filter(
                $filterValues,
                fn (string $filterValue) => $this->enumClass::fromDescription($filterValue) !== null
            )
        );
    }

    /**
     * Determine if all enum options have been specified.
     *
     * @param  array  $filterValues
     * @return bool
     */
    public function isAllFilterValues(array $filterValues): bool
    {
        return count($filterValues) === count($this->enumClass::getInstances());
    }

    /**
     * Get the validation rules for the filter.
     *
     * @return array
     */
    public function getRules(): array
    {
        return [
            new EnumDescriptionRule($this->enumClass),
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
        ];
    }
}
