<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Contracts\Http\Requests\Api\SearchableRequest;
use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Paging\Criteria as PagingCriteria;
use App\Http\Api\Criteria\Paging\LimitCriteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SearchParser;
use App\Http\Api\Parser\SortParser;
use App\Rules\Api\DistinctIgnoringDirectionRule;
use App\Rules\Api\RandomSoleRule;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\ValidationRules\Rules\Delimited;

/**
 * Class BaseRequest.
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
            PagingParser::$param => [
                'sometimes',
                'required',
                Str::of('array:')
                    ->append(OffsetCriteria::NUMBER_PARAM)
                    ->append(',')
                    ->append(OffsetCriteria::SIZE_PARAM)
                    ->__toString(),
            ],
            Str::of(PagingParser::$param)
                ->append('.')
                ->append(LimitCriteria::PARAM)
                ->__toString() => [
                    'prohibited',
                ],
            Str::of(PagingParser::$param)
                ->append('.')
                ->append(OffsetCriteria::NUMBER_PARAM)
                ->__toString() => [
                    'sometimes',
                    'required',
                    'integer',
                    'min:1',
                ],
            Str::of(PagingParser::$param)
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
            return [
                SearchParser::$param => [
                    'sometimes',
                    'required',
                    'string',
                ],
            ];
        }

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
        $allowedSorts = collect();

        foreach ($this->getSchema()->sorts() as $sort) {
            foreach (Direction::getInstances() as $direction) {
                $formattedSort = $sort->format($direction);
                if (! $allowedSorts->contains($formattedSort)) {
                    $allowedSorts->push($formattedSort);
                }
            }
        }

        if ($allowedSorts->isEmpty()) {
            return [
                SortParser::$param => [
                    'prohibited',
                ],
            ];
        }

        return [
            SortParser::$param => [
                'sometimes',
                'required',
                new Delimited(Rule::in($allowedSorts)),
                new DistinctIgnoringDirectionRule(),
                new RandomSoleRule(),
            ],
        ];
    }
}
