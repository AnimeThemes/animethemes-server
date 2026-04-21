<?php

declare(strict_types=1);

namespace App\GraphQL\Filter;

use App\Contracts\GraphQL\EnumFilterableColumns;
use App\Enums\GraphQL\Filter\Clause;
use Illuminate\Support\Facades\Validator;
use UnitEnum;

abstract class Filter
{
    public function __construct(
        protected UnitEnum&EnumFilterableColumns $enumCase,
        protected readonly string $column,
        protected readonly Clause $clause = Clause::WHERE,
    ) {}

    public function getColumn(): string
    {
        return $this->column;
    }

    public function getClause(): Clause
    {
        return $this->clause;
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
                [$this->enumCase->name => $filterValue],
                [$this->enumCase->name => $this->getRules()],
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
