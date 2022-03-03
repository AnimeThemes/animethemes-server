<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki;

use App\Http\Api\Criteria\Paging\Criteria as PagingCriteria;
use App\Http\Api\Criteria\Paging\LimitCriteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SearchParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query\Wiki\SearchQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\SearchSchema;
use App\Http\Requests\Api\BaseRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

/**
 * Class SearchRequest.
 */
class SearchRequest extends BaseRequest
{
    /**
     * Get the field validation rules.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getFieldRules(): array
    {
        $schema = $this->schema();

        $types = Arr::wrap($schema->type());

        $rules = $this->restrictAllowedFieldValues($schema);

        foreach ($schema->allowedIncludes() as $allowedInclude) {
            $resourceSchema = $allowedInclude->schema();

            $types[] = $resourceSchema->type();

            $rules = $rules + $this->restrictAllowedFieldValues($resourceSchema);

            foreach ($resourceSchema->allowedIncludes() as $resourceAllowedIncludePath) {
                $resourceRelationSchema = $resourceAllowedIncludePath->schema();

                $types[] = $resourceRelationSchema->type();

                $rules = $rules + $this->restrictAllowedFieldValues($resourceRelationSchema);
            }
        }

        return $rules + $this->restrictAllowedTypes(FieldParser::param(), collect($types));
    }

    /**
     * Get include validation rules.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getIncludeRules(): array
    {
        $schema = $this->schema();

        $types = collect();

        $rules = [];

        foreach ($schema->allowedIncludes() as $allowedInclude) {
            $resourceSchema = $allowedInclude->schema();

            $resourceIncludes = collect($resourceSchema->allowedIncludes());

            if ($resourceIncludes->isNotEmpty()) {
                $types->push($resourceSchema->type());

                $param = Str::of(IncludeParser::param())->append('.')->append($resourceSchema->type())->__toString();

                $rules = $rules + $this->restrictAllowedIncludeValues($param, $resourceSchema);
            }
        }

        return $rules + $this->restrictAllowedTypes(IncludeParser::param(), $types);
    }

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
                    ->append(LimitCriteria::PARAM)
                    ->__toString(),
            ],
            Str::of(PagingParser::param())
                ->append('.')
                ->append(OffsetCriteria::SIZE_PARAM)
                ->__toString() => [
                    'prohibited',
                ],
            Str::of(PagingParser::param())
                ->append('.')
                ->append(OffsetCriteria::NUMBER_PARAM)
                ->__toString() => [
                    'prohibited',
                ],
            Str::of(PagingParser::param())
                ->append('.')
                ->append(LimitCriteria::PARAM)
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
        return $this->require(SearchParser::param(), ['string']);
    }

    /**
     * Get the sort validation rules.
     *
     * @return array
     */
    protected function getSortRules(): array
    {
        $schema = $this->schema();

        $types = collect();

        $rules = [];

        foreach ($schema->allowedIncludes() as $allowedInclude) {
            $resourceSchema = $allowedInclude->schema();

            $types->push($resourceSchema->type());

            $param = Str::of(SortParser::param())->append('.')->append($resourceSchema->type())->__toString();

            $rules = $rules + $this->restrictAllowedSortValues($param, $resourceSchema);

            foreach ($resourceSchema->allowedIncludes() as $resourceAllowedIncludePath) {
                $resourceRelationSchema = $resourceAllowedIncludePath->schema();

                $types->push($resourceRelationSchema->type());

                $param = Str::of(SortParser::param())->append('.')->append($resourceRelationSchema->type())->__toString();

                $rules = $rules + $this->restrictAllowedSortValues($param, $resourceRelationSchema);
            }
        }

        return $rules + $this->restrictAllowedTypes(SortParser::param(), $types);
    }

    /**
     * Get the validation API Query.
     *
     * @return SearchQuery
     */
    public function getQuery(): SearchQuery
    {
        return new SearchQuery($this->validated());
    }

    /**
     * Get the schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new SearchSchema();
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

        foreach ($schema->allowedIncludes() as $allowedInclude) {
            $resourceSchema = $allowedInclude->schema();

            foreach ($resourceSchema->filters() as $resourceFilter) {
                $this->conditionallyRestrictFilter($validator, $resourceSchema, $resourceFilter);
            }

            foreach ($resourceSchema->allowedIncludes() as $resourceAllowedIncludePath) {
                $resourceRelationSchema = $resourceAllowedIncludePath->schema();

                foreach ($resourceRelationSchema->filters() as $resourceRelationFilter) {
                    $this->conditionallyRestrictFilter($validator, $resourceRelationSchema, $resourceRelationFilter);
                }
            }
        }
    }
}
