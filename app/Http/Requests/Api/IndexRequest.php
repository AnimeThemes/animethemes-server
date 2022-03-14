<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Contracts\Http\Requests\Api\SearchableRequest;
use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Http\Api\Filter\HasFilter;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\SearchParser;
use App\Http\Api\Parser\SortParser;
use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Spatie\ValidationRules\Rules\Delimited;

/**
 * Class IndexRequest.
 */
abstract class IndexRequest extends BaseRequest
{
    /**
     * Get the filter validation rules.
     *
     * @return array
     */
    protected function getFilterRules(): array
    {
        $schema = $this->schema();
        $types = collect(Arr::wrap($schema->type()));

        $schemaFormattedFilters = $this->getSchemaFormattedFilters($schema)
            ->concat($this->getFilterFormats(new HasFilter($schema->allowedIncludes()), BinaryLogicalOperator::getInstances()));

        $param = Str::of(FilterParser::param())->append('.')->append($schema->type())->__toString();
        $rules = $this->restrictAllowedTypes($param, $schemaFormattedFilters);

        $types = $types->concat($schemaFormattedFilters);

        foreach ($schema->allowedIncludes() as $allowedInclude) {
            $relationSchema = $allowedInclude->schema();
            $types->push($relationSchema->type());

            $relationSchemaFormattedFilters = $this->getSchemaFormattedFilters($relationSchema);
            $types = $types->concat($relationSchemaFormattedFilters);

            $param = Str::of(FilterParser::param())->append('.')->append($relationSchema->type())->__toString();
            $rules = $rules + $this->restrictAllowedTypes($param, $relationSchemaFormattedFilters);
        }

        return $rules + $this->restrictAllowedTypes(FilterParser::param(), $types->unique());
    }

    /**
     * Get the paging validation rules.
     *
     * @return array
     */
    protected function getPagingRules(): array
    {
        return $this->offset();
    }

    /**
     * Get the search validation rules.
     *
     * @return array
     */
    protected function getSearchRules(): array
    {
        if ($this instanceof SearchableRequest) {
            return $this->optional(SearchParser::param());
        }

        return $this->prohibit(SearchParser::param());
    }

    /**
     * Get the sort validation rules.
     *
     * @return array
     */
    protected function getSortRules(): array
    {
        $schema = $this->schema();

        $param = Str::of(SortParser::param())->append('.')->append($schema->type())->__toString();
        $rules = $this->restrictAllowedSortValues($param, $schema);

        foreach ($schema->allowedIncludes() as $allowedInclude) {
            $relationSchema = $allowedInclude->schema();

            $param = Str::of(SortParser::param())->append('.')->append($relationSchema->type())->__toString();
            $rules = $rules + $this->restrictAllowedSortValues($param, $relationSchema);
        }

        return $rules;
    }

    /**
     * Configure validator with needed conditional validation.
     *
     * @param  Validator  $validator
     * @return void
     */
    protected function handleConditionalValidation(Validator $validator): void
    {
        parent::handleConditionalValidation($validator);

        $this->conditionallyRestrictAllowedSortTypes($validator);
    }

    /**
     * Filters shall be validated based on values.
     * If the value contains a separator, this is a multi-value filter that builds a where in clause.
     * Otherwise, this is a single-value filter that builds a where clause.
     * Logical operators apply to specific clauses, so we must check formatted filter parameters against filter values.
     *
     * @param  Validator  $validator
     * @return void
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function conditionallyRestrictAllowedFilterValues(Validator $validator): void
    {
        $schema = $this->schema();

        foreach ($schema->filters() as $filter) {
            $this->conditionallyRestrictFilter($validator, $schema, $filter);
        }
        $this->conditionallyRestrictFilter($validator, $schema, new HasFilter($schema->allowedIncludes()));

        foreach ($schema->allowedIncludes() as $allowedInclude) {
            $relationSchema = $allowedInclude->schema();
            foreach ($relationSchema->filters() as $relationFilter) {
                $this->conditionallyRestrictFilter($validator, $relationSchema, $relationFilter);
            }
        }
    }

    /**
     * Sort types can be scoped globally or by type for index endpoints.
     * Ex: /api/anime?sort=year.
     * Ex: /api/anime?sort[anime]=year.
     *
     * @param  Validator  $validator
     * @return void
     */
    protected function conditionallyRestrictAllowedSortTypes(Validator $validator): void
    {
        $schema = $this->schema();
        $types = Arr::wrap($schema->type());

        foreach ($schema->allowedIncludes() as $allowedInclude) {
            $relationSchema = $allowedInclude->schema();
            $types[] = $relationSchema->type();
        }

        $validator->sometimes(
            SortParser::param(),
            ['sometimes', 'required', new Delimited(Rule::in($this->formatAllowedSortValues($schema)))],
            fn (Fluent $fluent) => is_string($fluent->get(SortParser::param()))
        );

        $validator->sometimes(
            SortParser::param(),
            ['nullable', Str::of('array:')->append(collect($types)->join(','))->__toString()],
            fn (Fluent $fluent) => is_array($fluent->get(SortParser::param()))
        );
    }
}
