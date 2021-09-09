<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SearchParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Resources\BaseResource;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Class ShowRequest.
 */
abstract class ShowRequest extends BaseRequest
{
    /**
     * Get the underlying resource.
     *
     * @return BaseResource
     */
    abstract protected function getResource(): BaseResource;

    /**
     * Get the include validation rules.
     *
     * @return array
     */
    protected function getIncludeRules(): array
    {
        $resource = $this->getResource();

        $allowedIncludePaths = $resource::allowedIncludePaths();

        if (empty($allowedIncludePaths)) {
            return [
                IncludeParser::$param => [
                    'prohibited',
                ],
            ];
        }

        return [
            IncludeParser::$param => [
                'nullable',
                'array',
                Rule::in($allowedIncludePaths),
            ],
            Str::of(IncludeParser::$param)
                ->append('.*')
                ->__toString() => [
                'distinct',
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
                'prohibited',
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
                'prohibited',
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
                'prohibited',
            ],
        ];
    }
}
