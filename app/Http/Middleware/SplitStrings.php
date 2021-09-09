<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Api\Criteria\Paging\LimitCriteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SearchParser;
use Illuminate\Foundation\Http\Middleware\TransformsRequest;
use Illuminate\Support\Str;

/**
 * Class SplitStrings.
 */
class SplitStrings extends TransformsRequest
{
    /**
     * The attributes that should not be trimmed.
     *
     * @return array
     */
    protected function except(): array
    {
        return [
            Str::of(PagingParser::$param)->append('.')->append(OffsetCriteria::NUMBER_PARAM)->__toString(),
            Str::of(PagingParser::$param)->append('.')->append(OffsetCriteria::SIZE_PARAM)->__toString(),
            Str::of(PagingParser::$param)->append('.')->append(LimitCriteria::PARAM)->__toString(),
            SearchParser::$param,
        ];
    }

    /**
     * Transform the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function transform($key, $value): mixed
    {
        if (in_array($key, $this->except(), true)) {
            return $value;
        }

        return is_string($value) ? Str::of($value)->explode(',')->all() : $value;
    }
}
