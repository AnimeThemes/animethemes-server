<?php

declare(strict_types=1);

namespace App\GraphQL\Filter;

use App\Enums\GraphQL\Filter\Clause;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\GraphQL\Argument\Argument;
use App\GraphQL\Argument\FilterArgument;
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

    /**
     * Allow the Equal operator to the filter.
     */
    public function useEq(mixed $defaultValue = null): static
    {
        $this->arguments[] = new FilterArgument($this->fieldName, $this->getBaseType(), ComparisonOperator::EQ)
            ->withDefaultValue($defaultValue);

        return $this;
    }

    /**
     * Allow the Greater operator to the filter.
     */
    public function useGt(mixed $defaultValue = null): static
    {
        $this->arguments[] = new FilterArgument($this->fieldName.'_greater', $this->getBaseType(), ComparisonOperator::GT)
            ->withDefaultValue($defaultValue);

        return $this;
    }

    /**
     * Allow the Lesser operator to the filter.
     */
    public function useLt(mixed $defaultValue = null): static
    {
        $this->arguments[] = new FilterArgument($this->fieldName.'_lesser', $this->getBaseType(), ComparisonOperator::LT)
            ->withDefaultValue($defaultValue);

        return $this;
    }

    /**
     * Allow the Like operator to the filter.
     */
    public function useLike(mixed $defaultValue = null): static
    {
        $this->arguments[] = new FilterArgument($this->fieldName.'_like', $this->getBaseType(), ComparisonOperator::LIKE)
            ->withDefaultValue($defaultValue);

        return $this;
    }

    /**
     * Allow the Not Like operator to the filter.
     */
    public function useNotLike(mixed $defaultValue = null): static
    {
        $this->arguments[] = new FilterArgument($this->fieldName.'_not_like', $this->getBaseType(), ComparisonOperator::NOTLIKE)
            ->withDefaultValue($defaultValue);

        return $this;
    }

    /**
     * Allow the IN operator to the filter.
     */
    public function useIn(mixed $defaultValue = null): static
    {
        $this->arguments[] = new Argument($this->fieldName.'_in', Type::listOf(Type::nonNull($this->getBaseType())))
            ->withDefaultValue($defaultValue);

        return $this;
    }

    /**
     * Allow the NOT IN operator to the filter.
     */
    public function useNotIn(mixed $defaultValue = null): static
    {
        $this->arguments[] = new Argument($this->fieldName.'_not_in', Type::listOf(Type::nonNull($this->getBaseType())))
            ->withDefaultValue($defaultValue);

        return $this;
    }

    abstract public function getBaseType(): Type;
}
