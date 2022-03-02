<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime;

use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SearchParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Query\Wiki\AnimeQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Requests\Api\BaseRequest;
use Illuminate\Validation\Validator;

/**
 * Class YearIndexRequest.
 */
class YearIndexRequest extends BaseRequest
{
    /**
     * Get include validation rules.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getIncludeRules(): array
    {
        return $this->prohibit(IncludeParser::param());
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
        return $this->prohibit(SortParser::param());
    }

    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new AnimeSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return Query
     */
    public function getQuery(): Query
    {
        return new AnimeQuery($this->validated());
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
        //
    }
}
