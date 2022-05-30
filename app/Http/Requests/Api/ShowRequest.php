<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SearchParser;
use App\Http\Api\Parser\SortParser;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

/**
 * Class ShowRequest.
 */
abstract class ShowRequest extends ReadRequest
{
    /**
     * Get the filter validation rules.
     *
     * @return array
     */
    protected function getFilterRules(): array
    {
        $schema = $this->schema();

        $allowedIncludes = $schema->allowedIncludes();
        if (empty($allowedIncludes)) {
            return $this->prohibit(FilterParser::param());
        }

        $rules = [];
        $types = [];

        foreach ($allowedIncludes as $allowedInclude) {
            $relationSchema = $allowedInclude->schema();
            $types[] = $relationSchema->type();

            $relationSchemaFormattedFilters = $this->getSchemaFormattedFilters($relationSchema);
            $types = array_merge($types, $relationSchemaFormattedFilters);

            $param = Str::of(FilterParser::param())->append('.')->append($relationSchema->type())->__toString();
            $rules = $rules + $this->restrictAllowedTypes($param, $relationSchemaFormattedFilters);
        }

        return $rules + $this->restrictAllowedTypes(FilterParser::param(), array_unique($types));
    }

    /**
     * Get the paging validation rules.
     *
     * @return array
     */
    protected function getPagingRules(): array
    {
        return $this->prohibit(PagingParser::param());
    }

    /**
     * Get the search validation rules.
     *
     * @return array
     */
    protected function getSearchRules(): array
    {
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

        $allowedIncludes = $schema->allowedIncludes();
        if (empty($allowedIncludes)) {
            return $this->prohibit(SortParser::param());
        }

        $rules = [];
        $types = [];

        foreach ($allowedIncludes as $allowedInclude) {
            $relationSchema = $allowedInclude->schema();
            $types[] = $relationSchema->type();

            $param = Str::of(SortParser::param())->append('.')->append($relationSchema->type())->__toString();
            $rules = $rules + $this->restrictAllowedSortValues($param, $relationSchema);
        }

        return $rules + $this->restrictAllowedTypes(SortParser::param(), $types);
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
        foreach ($schema->allowedIncludes() as $allowedInclude) {
            $relationSchema = $allowedInclude->schema();
            foreach ($relationSchema->filters() as $relationFilter) {
                $this->conditionallyRestrictFilter($validator, $relationSchema, $relationFilter);
            }
        }
    }
}
