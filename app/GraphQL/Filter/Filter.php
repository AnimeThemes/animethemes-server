<?php

declare(strict_types=1);

namespace App\GraphQL\Filter;

use Illuminate\Support\Facades\Validator;

abstract class Filter
{
    public function __construct(
        protected string $enumName,
        protected readonly string $column,
    ) {}

    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * Get sanitized filter values.
     */
    public function getFilterValues(array $attemptedFilterValues): array
    {
        $this->validateFilterValues($attemptedFilterValues);

        return $this->convertFilterValues($attemptedFilterValues);
    }

    /**
     * Convert filter values if needed. By default, no conversion is needed.
     */
    abstract protected function convertFilterValues(array $filterValues): array;

    /**
     * Validate the filter values against its rules based on types.
     */
    protected function validateFilterValues(array $filterValues): void
    {
        foreach ($filterValues as $filterValue) {
            Validator::make(
                [$this->enumName => $filterValue],
                [$this->enumName => $this->getRules()],
            )->validate();
        }
    }

    /**
     * Get the validation rules for the filter.
     */
    protected function getRules(): array
    {
        return [];
    }
}
