<?php

declare(strict_types=1);

namespace App\Concerns\Http\Requests\Api;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\LogicalOperator;
use App\Enums\Http\Api\Filter\UnaryLogicalOperator;
use App\Http\Api\Criteria\Filter\Criteria;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Schema\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use Spatie\ValidationRules\Rules\Delimited;

/**
 * Trait ValidatesFilters.
 */
trait ValidatesFilters
{
    use ValidatesParameters;

    /**
     * Get the list of formatted filters for the schema.
     *
     * @param  Schema  $schema
     * @return Collection
     */
    protected function getSchemaFormattedFilters(Schema $schema): Collection
    {
        $schemaFilters = collect();

        foreach ($schema->filters() as $filter) {
            $schemaFilters = $schemaFilters->concat($this->getFilterFormats($filter, BinaryLogicalOperator::getInstances()));
            $schemaFilters = $schemaFilters->concat($this->getFilterFormats($filter, UnaryLogicalOperator::getInstances()));
        }

        return $schemaFilters->filter()->unique();
    }

    /**
     * Get the allowed list of filter keys with possible conditions.
     *
     * @param  Filter  $filter
     * @param  LogicalOperator[]  $logicalOperators
     * @return array
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
     * @param  Schema  $schema
     * @param  string  $formattedFilter
     * @return array
     */
    protected function getFormattedParameters(Schema $schema, string $formattedFilter): array
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
     *
     * @param  Validator  $validator
     * @param  Schema  $schema
     * @param  Filter  $filter
     * @return void
     */
    protected function conditionallyRestrictFilter(Validator $validator, Schema $schema, Filter $filter): void
    {
        $singleValueFilterFormats = $this->getFilterFormats($filter, BinaryLogicalOperator::getInstances());
        foreach ($singleValueFilterFormats as $singleValueFilterFormat) {
            foreach ($this->getFormattedParameters($schema, $singleValueFilterFormat) as $formattedParameter) {
                if (collect($validator->getRules())->keys()->doesntContain($formattedParameter)) {
                    $validator->sometimes(
                        $formattedParameter,
                        $filter->getRules(),
                        fn (Fluent $fluent) => is_string(Arr::get($fluent->toArray(), $formattedParameter)) && ! Str::of(Arr::get($fluent->toArray(), $formattedParameter))->contains(Criteria::VALUE_SEPARATOR)
                    );
                }
            }
        }

        $multiValueRules = [];
        foreach ($filter->getRules() as $rule) {
            $multiValueRules[] = new Delimited($rule);
        }

        $multiValueFilterFormats = $this->getFilterFormats($filter, UnaryLogicalOperator::getInstances());
        foreach ($multiValueFilterFormats as $multiValueFilterFormat) {
            foreach ($this->getFormattedParameters($schema, $multiValueFilterFormat) as $formattedParameter) {
                if (collect($validator->getRules())->keys()->doesntContain($formattedParameter)) {
                    $validator->sometimes(
                        $formattedParameter,
                        $multiValueRules,
                        fn (Fluent $fluent) => is_string(Arr::get($fluent->toArray(), $formattedParameter)) && Str::of(Arr::get($fluent->toArray(), $formattedParameter))->contains(Criteria::VALUE_SEPARATOR)
                    );
                }
            }
        }
    }
}
