<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki;

use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SearchParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query\Wiki\SearchQuery;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class SearchRequest.
 */
class SearchRequest extends FormRequest
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
     * Get the field validation rules.
     *
     * @return array
     */
    protected function getFieldRules(): array
    {
        return [
            FieldParser::$param => [
                'nullable',
            ],
        ];
    }

    /**
     * Get the filter validation rules.
     *
     * @return array
     */
    protected function getFilterRules(): array
    {
        return [
            FilterParser::$param => [
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
        return [
            IncludeParser::$param => [
                'nullable',
            ],
        ];
    }

    /**
     * Get the paging validation rules.
     *
     * @return array
     */
    protected function getPagingRules(): array
    {
        return [
            PagingParser::$param => [
                'nullable',
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
            SearchParser::$param => [
                'required',
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
        return [
            SortParser::$param => [
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
        return SearchQuery::make($this->validated());
    }
}
