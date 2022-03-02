<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Contracts\Http\Requests\Api\SearchableRequest;
use App\Http\Api\Criteria\Paging\Criteria as PagingCriteria;
use App\Http\Api\Criteria\Paging\LimitCriteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SearchParser;
use App\Http\Api\Parser\SortParser;
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
     * Get the paging validation rules.
     *
     * @return array
     */
    protected function getPagingRules(): array
    {
        return [
            PagingParser::param() => [
                'sometimes',
                'required',
                Str::of('array:')
                    ->append(OffsetCriteria::NUMBER_PARAM)
                    ->append(',')
                    ->append(OffsetCriteria::SIZE_PARAM)
                    ->__toString(),
            ],
            Str::of(PagingParser::param())
                ->append('.')
                ->append(LimitCriteria::PARAM)
                ->__toString() => [
                    'prohibited',
                ],
            Str::of(PagingParser::param())
                ->append('.')
                ->append(OffsetCriteria::NUMBER_PARAM)
                ->__toString() => [
                    'sometimes',
                    'required',
                    'integer',
                    'min:1',
                ],
            Str::of(PagingParser::param())
                ->append('.')
                ->append(OffsetCriteria::SIZE_PARAM)
                ->__toString() => [
                    'sometimes',
                    'required',
                    'integer',
                    'min:1',
                    Str::of('max:')->append(PagingCriteria::MAX_RESULTS)->__toString(),
                ],
        ];
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

        $types = collect($schema->type());

        $param = Str::of(SortParser::param())->append('.')->append($schema->type())->__toString();

        $rules = $this->restrictAllowedSortValues($param, $schema);

        foreach ($schema->allowedIncludes() as $allowedInclude) {
            $relationSchema = $allowedInclude->schema();

            $types->push($relationSchema->type());

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
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function handleConditionalValidation(Validator $validator): void
    {
        $this->conditionallyRestrictAllowedSortTypes($validator);
        $this->conditionallyRestrictAllowedFilterValues($validator);
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

        $types = collect($schema->type());

        foreach ($schema->allowedIncludes() as $allowedInclude) {
            $relationSchema = $allowedInclude->schema();

            $types->push($relationSchema->type());
        }

        $validator->sometimes(
            SortParser::param(),
            ['sometimes', 'required', new Delimited(Rule::in($this->formatAllowedSortValues($schema)))],
            fn (Fluent $fluent) => is_string($fluent->get(SortParser::param()))
        );

        $validator->sometimes(
            SortParser::param(),
            ['nullable', Str::of('array:')->append($types->join(','))->__toString()],
            fn (Fluent $fluent) => is_array($fluent->get(SortParser::param()))
        );
    }

    /**
     * Filters shall be validated based on values.
     * If the value contains a separator, this is a multi-value filter that builds a where in clause.
     * Otherwise, this is a single-value filter that builds a where clause.
     * Logical operators apply to specific clauses, so we must check formatted filter parameters against filter values.
     *
     * @param  Validator  $validator
     * @return void
     */
    protected function conditionallyRestrictAllowedFilterValues(Validator $validator): void
    {
        $schema = $this->schema();

        foreach ($schema->filters() as $filter) {
            $this->conditionallyRestrictFilter($validator, $schema, $filter);
        }

        foreach ($schema->allowedIncludes() as $allowedInclude) {
            $relationSchema = $allowedInclude->schema();

            foreach ($relationSchema->filters() as $relationFilter) {
                $this->conditionallyRestrictFilter($validator, $relationSchema, $relationFilter);
            }
        }
    }
}
