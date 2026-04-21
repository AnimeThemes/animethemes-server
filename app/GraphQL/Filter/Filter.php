<?php

declare(strict_types=1);

namespace App\GraphQL\Filter;

use App\Enums\GraphQL\Filter\Clause;
use App\GraphQL\Argument\Argument;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Validator;

abstract class Filter
{
    /**
     * @var Argument[]
     */
    protected array $arguments = [];

    public function __construct(
        protected readonly string $fieldName,
        protected readonly ?string $column = null,
        protected readonly Clause $clause = Clause::WHERE,
    ) {}

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getColumn(): string
    {
        return $this->column ?? $this->getFieldName();
    }

    public function getClause(): Clause
    {
        return $this->clause;
    }

    /**
     * @return Argument[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
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
                [$this->fieldName => $filterValue],
                [$this->fieldName => $this->getRules()],
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

    abstract public function getBaseType(): Type;
}
