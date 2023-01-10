<?php

declare(strict_types=1);

namespace App\Http\Api\Filter;

use App\Enums\Http\Api\Filter\Clause;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Enums\Http\Api\Filter\LogicalOperator;
use App\Enums\Http\Api\QualifyColumn;
use App\Http\Api\Criteria\Filter\Criteria;
use Illuminate\Support\Str;

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
     * @param  QualifyColumn  $qualifyColumn
     * @param  Clause  $clause
     */
    public function __construct(
        protected readonly string $key,
        protected readonly ?string $column = null,
        protected readonly QualifyColumn $qualifyColumn = new QualifyColumn(QualifyColumn::YES),
        protected readonly Clause $clause = new Clause(Clause::WHERE)
    ) {
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
     * Determine if the column should be qualified for the filter.
     *
     * @return bool
     */
    public function shouldQualifyColumn(): bool
    {
        return QualifyColumn::YES()->is($this->qualifyColumn);
    }

    /**
     * Get filter clause.
     *
     * @return Clause
     */
    public function clause(): Clause
    {
        return $this->clause;
    }

    /**
     * Get sanitized filter values.
     *
     * @param  array  $attemptedFilterValues
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

    /**
     * Get the validation rules for the filter.
     *
     * @return array
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
     *
     * @param  LogicalOperator|null  $logicalOperator
     * @param  ComparisonOperator|null  $comparisonOperator
     * @return string
     */
    public function format(
        ?LogicalOperator $logicalOperator = null,
        ?ComparisonOperator $comparisonOperator = null
    ): string {
        $formattedFilter = Str::of($this->getKey());

        if ($comparisonOperator !== null) {
            $formattedFilter = $formattedFilter->append(Criteria::PARAM_SEPARATOR)
                ->append($comparisonOperator->key);
        }

        if ($logicalOperator !== null) {
            $formattedFilter = $formattedFilter->append(Criteria::PARAM_SEPARATOR)
                ->append($logicalOperator->key);
        }

        return $formattedFilter->lower()->__toString();
    }
}
