<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\LogicalOperator;
use App\Enums\Http\Api\Filter\UnaryLogicalOperator;
use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Filter\Criteria;
use App\Http\Api\Field\Field;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Rules\Api\DistinctIgnoringDirectionRule;
use App\Rules\Api\RandomSoleRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Spatie\ValidationRules\Rules\Delimited;

/**
 * Class BaseRequest.
 */
abstract class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return array_merge(
            $this->getFieldRules(),
            $this->getFilterRules(),
            $this->getIncludeRules(),
            $this->getPagingRules(),
            $this->getSearchRules(),
            $this->getSortRules(),
        );
    }

    /**
     * Restrict the allowed types for the parameter.
     *
     * @param  string  $param
     * @param  Collection  $types
     * @return array
     */
    protected function restrictAllowedTypes(string $param, Collection $types): array
    {
        return [
            $param => [
                'nullable',
                Str::of('array:')->append($types->join(','))->__toString(),
            ],
        ];
    }

    /**
     * Restrict the allowed values for the parameter.
     *
     * @param  string  $param
     * @param  Collection  $values
     * @param  array  $customRules
     * @return array
     */
    protected function restrictAllowedValues(string $param, Collection $values, array $customRules = []): array
    {
        return [
            $param => array_merge(
                ['sometimes', 'required', new Delimited(Rule::in($values))],
                $customRules,
            ),
        ];
    }

    /**
     * Prohibit the parameter.
     *
     * @param  string  $param
     * @return array
     */
    protected function prohibit(string $param): array
    {
        return [
            $param => [
                'prohibited',
            ],
        ];
    }

    /**
     * Optional parameter.
     *
     * @param  string  $param
     * @param  array  $customRules
     * @return array
     */
    protected function optional(string $param, array $customRules = []): array
    {
        return [
            $param => array_merge(
                ['sometimes', 'required'],
                $customRules,
            ),
        ];
    }

    /**
     * Require the parameter.
     *
     * @param  string  $param
     * @param  array  $customRules
     * @return array
     */
    protected function require(string $param, array $customRules = []): array
    {
        return [
            $param => array_merge(
                ['required'],
                $customRules,
            ),
        ];
    }

    /**
     * Get the field validation rules.
     *
     * @return array
     */
    protected function getFieldRules(): array
    {
        $schema = $this->schema();

        $types = Arr::wrap($schema->type());

        $rules = $this->restrictAllowedFieldValues($schema);

        foreach ($schema->allowedIncludes() as $allowedInclude) {
            $relationSchema = $allowedInclude->schema();

            $types[] = $relationSchema->type();

            $rules = $rules + $this->restrictAllowedFieldValues($relationSchema);
        }

        return $rules + $this->restrictAllowedTypes(FieldParser::param(), collect($types));
    }

    /**
     * Restrict the allowed values for the schema fields.
     *
     * @param  Schema  $schema
     * @return array[]
     */
    protected function restrictAllowedFieldValues(Schema $schema): array
    {
        return $this->restrictAllowedValues(
            Str::of(FieldParser::param())->append('.')->append($schema->type())->__toString(),
            collect($schema->fields())->map(fn (Field $field) => $field->getKey())
        );
    }

    /**
     * Get the filter validation rules.
     *
     * @return array
     */
    protected function getFilterRules(): array
    {
        // TODO: placeholder so that filter is passed by form request as validated to DTO
        return [
            FilterParser::param() => [
                'nullable',
            ],
        ];
    }

    /**
     * Get include validation rules.
     *
     * @return array
     */
    protected function getIncludeRules(): array
    {
        $schema = $this->schema();

        if (collect($schema->allowedIncludes())->isEmpty()) {
            return $this->prohibit(IncludeParser::param());
        }

        return $this->restrictAllowedIncludeValues(IncludeParser::param(), $schema);
    }

    /**
     * Restrict the allowed values for the schema includes.
     *
     * @param  string  $param
     * @param  Schema  $schema
     * @return array[]
     */
    protected function restrictAllowedIncludeValues(string $param, Schema $schema): array
    {
        return $this->restrictAllowedValues(
            $param,
            collect($schema->allowedIncludes())->map(fn (AllowedInclude $include) => $include->path())
        );
    }

    /**
     * Get allowed sorts for schema.
     *
     * @param  Schema  $schema
     * @return Collection
     */
    protected function formatAllowedSortValues(Schema $schema): Collection
    {
        $allowedSorts = collect();

        foreach ($schema->sorts() as $sort) {
            foreach (Direction::getInstances() as $direction) {
                $formattedSort = $sort->format($direction);
                if (! $allowedSorts->contains($formattedSort)) {
                    $allowedSorts->push($formattedSort);
                }
            }
        }

        return $allowedSorts;
    }

    /**
     * Restrict allowed sorts for schema.
     *
     * @param  string  $param
     * @param  Schema  $schema
     * @return array[]
     */
    protected function restrictAllowedSortValues(string $param, Schema $schema): array
    {
        return $this->restrictAllowedValues(
            $param,
            $this->formatAllowedSortValues($schema),
            [new DistinctIgnoringDirectionRule(), new RandomSoleRule()]
        );
    }

    /**
     * Get the paging validation rules.
     *
     * @return array
     */
    abstract protected function getPagingRules(): array;

    /**
     * Get the search validation rules.
     *
     * @return array
     */
    abstract protected function getSearchRules(): array;

    /**
     * Get the sort validation rules.
     *
     * @return array
     */
    abstract protected function getSortRules(): array;

    /**
     * Get the schema.
     *
     * @return Schema
     */
    abstract protected function schema(): Schema;

    /**
     * Get the validation API Query.
     *
     * @return Query
     */
    abstract public function getQuery(): Query;

    /**
     * Configure the validator instance.
     * Note: This function is invoked by name if it exists.
     * We define a decorator that provides better clarity for what conditional validation needs to be applied.
     *
     * @param  Validator  $validator
     * @return void
     *
     * @noinspection PhpUnused
     */
    public function withValidator(Validator $validator): void
    {
        $this->handleConditionalValidation($validator);
    }

    /**
     * Configure conditional validation.
     *
     * @param  Validator  $validator
     * @return void
     */
    protected function handleConditionalValidation(Validator $validator): void
    {
        $this->conditionallyRestrictAllowedFilterValues($validator);
    }

    /**
     * Filters shall be validated based on values.
     * If the value contains a separator, we assume this is a multi-value filter that builds a where in clause.
     * Otherwise, we assume this is a single-value filter that builds a where clause.
     * Logical operators apply to specific clauses, so we must check formatted filter parameters against filter values.
     *
     * @param  Validator  $validator
     * @return void
     */
    abstract protected function conditionallyRestrictAllowedFilterValues(Validator $validator): void;

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

        $multiValueFilterFormats = $this->getFilterFormats($filter, UnaryLogicalOperator::getInstances());

        $multiValueRules = [];

        foreach ($filter->getRules() as $rule) {
            $multiValueRules[] = new Delimited($rule);
        }

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

        return $formattedFilters;
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
}
