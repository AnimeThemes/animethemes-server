<?php

declare(strict_types=1);

namespace App\Concerns\Http\Requests\Api;

use App\Contracts\Http\Api\Schema\SchemaInterface;
use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\Clause;
use App\Enums\Http\Api\Filter\UnaryLogicalOperator;
use App\Http\Api\Criteria\Filter\Criteria;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Parser\FilterParser;
use App\Rules\Api\DelimitedRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

trait ValidatesFilters
{
    use ValidatesParameters;

    /**
     * Get the list of formatted filters for the schema.
     *
     * @return string[]
     */
    protected function getSchemaFormattedFilters(SchemaInterface $schema): array
    {
        $schemaFilters = [];

        foreach ($schema->filters() as $filter) {
            $schemaFilters = array_merge(
                $schemaFilters,
                $this->getFilterFormats($filter, BinaryLogicalOperator::cases()),
                $this->getFilterFormats($filter, UnaryLogicalOperator::cases())
            );
        }

        return array_unique(array_filter($schemaFilters));
    }

    /**
     * Get the allowed list of filter keys with possible conditions.
     *
     * @param  array<int, BinaryLogicalOperator|UnaryLogicalOperator>  $logicalOperators
     * @return string[]
     */
    protected function getFilterFormats(Filter $filter, array $logicalOperators): array
    {
        $formattedFilters = [];

        foreach ($logicalOperators as $binaryLogicalOperator) {
            foreach ($filter->getAllowedComparisonOperators() as $allowedComparisonOperator) {
                $formattedFilters[] = $filter->format($binaryLogicalOperator, $allowedComparisonOperator);
            }
            $formattedFilters[] = $filter->format($binaryLogicalOperator);
        }

        foreach ($filter->getAllowedComparisonOperators() as $allowedComparisonOperator) {
            $formattedFilters[] = $filter->format(null, $allowedComparisonOperator);
        }

        $formattedFilters[] = $filter->format();

        return array_unique(array_filter($formattedFilters));
    }

    /**
     * Get possible qualified parameter values for formatted filter.
     *
     * @return string[]
     */
    protected function getFormattedParameters(SchemaInterface $schema, string $formattedFilter): array
    {
        return [
            Str::of(FilterParser::param())
                ->append('.')
                ->append($formattedFilter)
                ->__toString(),

            Str::of(FilterParser::param())
                ->append('.')
                ->append($schema->type())
                ->append('.')
                ->append($formattedFilter)
                ->__toString(),
        ];
    }

    /**
     * Restrict filter based on allowed formats and provided values.
     */
    protected function conditionallyRestrictFilter(Validator $validator, SchemaInterface $schema, Filter $filter): void
    {
        $singleValueFilterFormats = $this->getFilterFormats($filter, BinaryLogicalOperator::cases());
        foreach ($singleValueFilterFormats as $singleValueFilterFormat) {
            foreach ($this->getFormattedParameters($schema, $singleValueFilterFormat) as $formattedParameter) {
                if (collect($validator->getRules())->keys()->doesntContain($formattedParameter)) {
                    $validator->sometimes(
                        $formattedParameter,
                        $filter->getRules(),
                        fn (Fluent $fluent) => Arr::has($fluent->toArray(), $formattedParameter) && ! Str::of(Arr::get($fluent->toArray(), $formattedParameter))->contains(Criteria::VALUE_SEPARATOR)
                    );
                }
            }
        }

        if ($filter->clause() === Clause::WHERE) {
            $this->validateMultiValueFilterForWhereClause($validator, $schema, $filter);
        }

        if ($filter->clause() === Clause::HAVING) {
            $this->prohibitMultiValueFilterForHavingClause($validator, $schema, $filter);
        }
    }

    /**
     * Restrict where in clause based on allowed formats and provided values.
     */
    protected function validateMultiValueFilterForWhereClause(Validator $validator, SchemaInterface $schema, Filter $filter): void
    {
        $multiValueRules = [];
        foreach ($filter->getRules() as $rule) {
            $multiValueRules[] = new DelimitedRule($rule);
        }

        $multiValueFilterFormats = $this->getFilterFormats($filter, UnaryLogicalOperator::cases());
        foreach ($multiValueFilterFormats as $multiValueFilterFormat) {
            foreach ($this->getFormattedParameters($schema, $multiValueFilterFormat) as $formattedParameter) {
                if (collect($validator->getRules())->keys()->doesntContain($formattedParameter)) {
                    $validator->sometimes(
                        $formattedParameter,
                        $multiValueRules,
                        fn (Fluent $fluent) => Arr::has($fluent->toArray(), $formattedParameter) && Str::of(Arr::get($fluent->toArray(), $formattedParameter))->contains(Criteria::VALUE_SEPARATOR)
                    );
                }
            }
        }
    }

    /**
     * Prohibit multi value filter in having clause which is not supported in the SQL standard.
     */
    protected function prohibitMultiValueFilterForHavingClause(Validator $validator, SchemaInterface $schema, Filter $filter): void
    {
        $multiValueFilterFormats = $this->getFilterFormats($filter, UnaryLogicalOperator::cases());
        foreach ($multiValueFilterFormats as $multiValueFilterFormat) {
            foreach ($this->getFormattedParameters($schema, $multiValueFilterFormat) as $formattedParameter) {
                if (collect($validator->getRules())->keys()->doesntContain($formattedParameter)) {
                    $validator->sometimes(
                        $formattedParameter,
                        ['prohibited'],
                        fn (Fluent $fluent) => Arr::has($fluent->toArray(), $formattedParameter) && Str::of(Arr::get($fluent->toArray(), $formattedParameter))->contains(Criteria::VALUE_SEPARATOR)
                    );
                }
            }
        }
    }
}
