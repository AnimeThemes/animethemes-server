<?php

declare(strict_types=1);

namespace App\Http\Api\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\Clause;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Enums\Http\Api\Filter\UnaryLogicalOperator;
use App\Enums\Http\Api\QualifyColumn;
use App\Http\Api\Criteria\Filter\Criteria;
use Illuminate\Support\Str;

abstract class Filter
{
    public function __construct(
        protected readonly string $key,
        protected readonly ?string $column = null,
        protected readonly QualifyColumn $qualifyColumn = QualifyColumn::YES,
        protected readonly Clause $clause = Clause::WHERE
    ) {}

    /**
     * Get filter key value.
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get filter column.
     */
    public function getColumn(): string
    {
        return $this->column ?? $this->key;
    }

    /**
     * Determine if the column should be qualified for the filter.
     */
    public function shouldQualifyColumn(): bool
    {
        return $this->qualifyColumn === QualifyColumn::YES;
    }

    /**
     * Get filter clause.
     */
    public function clause(): Clause
    {
        return $this->clause;
    }

    /**
     * Get sanitized filter values.
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
     */
    protected function getUniqueFilterValues(array $filterValues): array
    {
        return array_values(array_unique($filterValues));
    }

    /**
     * Convert filter values if needed. By default, no conversion is needed.
     */
    abstract protected function convertFilterValues(array $filterValues): array;

    /**
     * Get only filter values that are valid. By default, all values are valid.
     */
    abstract protected function getValidFilterValues(array $filterValues): array;

    /**
     * Determine if all valid filter values have been specified.
     * By default, this is false as we assume an unrestricted amount of valid values.
     */
    abstract public function isAllFilterValues(array $filterValues): bool;

    /**
     * Get the validation rules for the filter.
     */
    abstract public function getRules(): array;

    /**
     * Get the allowed comparison operators for the filter.
     *
     * @return ComparisonOperator[]
     */
    abstract public function getAllowedComparisonOperators(): array;

    /**
     * Format filter string with conditions.
     */
    public function format(
        BinaryLogicalOperator|UnaryLogicalOperator|null $logicalOperator = null,
        ?ComparisonOperator $comparisonOperator = null
    ): string {
        $formattedFilter = Str::of($this->getKey());

        if ($comparisonOperator instanceof ComparisonOperator) {
            $formattedFilter = $formattedFilter->append(Criteria::PARAM_SEPARATOR)
                ->append(Str::lower($comparisonOperator->name));
        }

        if ($logicalOperator !== null) {
            $formattedFilter = $formattedFilter->append(Criteria::PARAM_SEPARATOR)
                ->append(Str::lower($logicalOperator->name));
        }

        return $formattedFilter->lower()->__toString();
    }
}
