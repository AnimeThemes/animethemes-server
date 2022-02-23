<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki;

use App\Http\Api\Criteria\Paging\Criteria as PagingCriteria;
use App\Http\Api\Criteria\Paging\LimitCriteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SearchParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query\Wiki\SearchQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\SearchSchema;
use App\Http\Requests\Api\BaseRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\ValidationRules\Rules\Delimited;

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
        $schema = $this->getSchema();

        $types = collect($schema->type());

        $rules = $this->getSchemaFieldRules($schema);

        foreach ($schema->allowedIncludes() as $allowedIncludePath) {
            $resourceSchema = $allowedIncludePath->schema();

            $types->push($resourceSchema->type());

            $rules = array_merge($rules, array_merge($rules, $this->getSchemaFieldRules($resourceSchema)));

            foreach ($resourceSchema->allowedIncludes() as $resourceAllowedIncludePath) {
                $resourceRelationSchema = $resourceAllowedIncludePath->schema();

                $types->push($resourceRelationSchema->type());

                $rules = array_merge($rules, array_merge($rules, $this->getSchemaFieldRules($resourceRelationSchema)));
            }
        }

        return array_merge(
            $rules,
            [
                FieldParser::param() => [
                    'nullable',
                    Str::of('array:')->append($types->unique()->join(','))->__toString(),
                ],
            ],
        );
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
        $schema = $this->getSchema();

        $types = collect();

        $rules = [];

        foreach ($schema->allowedIncludes() as $allowedIncludePath) {
            $resourceSchema = $allowedIncludePath->schema();

            $resourceIncludes = collect($resourceSchema->allowedIncludes());

            if ($resourceIncludes->isNotEmpty()) {
                $types->push($resourceSchema->type());

                $rules = array_merge(
                    $rules,
                    [
                        Str::of(IncludeParser::param())
                            ->append('.')
                            ->append($resourceSchema->type())
                            ->__toString() => [
                            'sometimes',
                            'required',
                            new Delimited(Rule::in($resourceIncludes->map(fn (AllowedInclude $include) => $include->path()))),
                        ],
                    ]
                );
            }
        }

        return array_merge(
            $rules,
            [
                IncludeParser::param() => [
                    'nullable',
                    Str::of('array:')->append($types->join(','))->__toString(),
                ],
            ],
        );
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
        return [
            SearchParser::param() => [
                'required',
                'string',
            ],
        ];
    }

    /**
     * Get the sort validation rules.
     *
     * @return array
     */
    protected function getSortRules(): array
    {
        // TODO: sorts should be scoped to resource.
        return [
            SortParser::param() => [
                'nullable',
            ],
        ];
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
    protected function getSchema(): Schema
    {
        return new SearchSchema();
    }
}
