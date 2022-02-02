<?php

declare(strict_types=1);

namespace App\Http\Api\Filter;

/**
 * Class Filter.
 */
abstract class Filter
{
    /**
     * Create a new filter instance.
     *
     * @param  string  $key
     * @param  string|null  $column
     */
    public function __construct(protected string $key, protected ?string $column = null)
    {
    }

    /**
     * Get filter key value.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get filter column.
     *
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column ?? $this->key;
    }

    /**
     * Get sanitized filter values.
     *
     * @param array $attemptedFilterValues
     * @return array
     */
    public function getFilterValues(array $attemptedFilterValues): array
    {
        return $this->getUniqueFilterValues(
            $this->convertFilterValues(
                $this->getValidFilterValues(
                    $attemptedFilterValues
                )
            )
        );
    }

    /**
     * Get unique filter values.
     *
     * @param  array  $filterValues
     * @return array
     */
    protected function getUniqueFilterValues(array $filterValues): array
    {
        return array_values(array_unique($filterValues));
    }

    /**
     * Convert filter values if needed. By default, no conversion is needed.
     *
     * @param  array  $filterValues
     * @return array
     */
    abstract protected function convertFilterValues(array $filterValues): array;

    /**
     * Get only filter values that are valid. By default, all values are valid.
     *
     * @param  array  $filterValues
     * @return array
     */
    abstract protected function getValidFilterValues(array $filterValues): array;

    /**
     * Determine if all valid filter values have been specified.
     * By default, this is false as we assume an unrestricted amount of valid values.
     *
     * @param  array  $filterValues
     * @return bool
     */
    abstract public function isAllFilterValues(array $filterValues): bool;
}
