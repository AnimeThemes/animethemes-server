<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Paging\Criteria as PagingCriteria;
use App\Http\Api\Criteria\Paging\LimitCriteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Resources\BaseCollection;
use App\Rules\Api\DistinctIgnoringDirectionRule;
use App\Rules\Api\RandomSoleRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Class BaseRequest.
 */
abstract class IndexRequest extends BaseRequest
{
    /**
     * Get the underlying resource collection.
     *
     * @return BaseCollection
     */
    abstract protected function getCollection(): BaseCollection;

    /**
     * Get the include validation rules.
     *
     * @return array
     */
    protected function getIncludeRules(): array
    {
        $collection = $this->getCollection();

        $allowedIncludePaths = $collection::allowedIncludePaths();

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
                'nullable',
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
                    'nullable',
                    'integer',
                    'min:1',
                ],
            Str::of(PagingParser::$param)
                ->append('.')
                ->append(OffsetCriteria::SIZE_PARAM)
                ->__toString() => [
                    'nullable',
                    'integer',
                    'min:1',
                    Str::of('max:')->append(PagingCriteria::MAX_RESULTS)->__toString(),
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
        $collection = $this->getCollection();

        $sorts = $collection::sorts(Collection::make());

        if (empty($sorts)) {
            return [
                SortParser::$param => [
                    'prohibited',
                ],
            ];
        }

        $allowedSorts = collect();

        foreach ($sorts as $sort) {
            foreach (Direction::getInstances() as $direction) {
                $formattedSort = $sort->format($direction);
                if (! $allowedSorts->contains($formattedSort)) {
                    $allowedSorts->push($formattedSort);
                }
            }
        }

        return [
            SortParser::$param => [
                'nullable',
                'array',
                Rule::in($allowedSorts),
                new DistinctIgnoringDirectionRule(),
                new RandomSoleRule(),
            ],
            Str::of(SortParser::$param)
                ->append('.*')
                ->__toString() => [
                    'distinct',
                ],
        ];
    }
}
