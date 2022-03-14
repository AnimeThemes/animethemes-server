<?php

declare(strict_types=1);

namespace App\Concerns\Http\Requests\Api;

use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\LimitCriteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Parser\PagingParser;
use Illuminate\Support\Str;

/**
 * Trait ValidatesPaging.
 */
trait ValidatesPaging
{
    use ValidatesParameters;

    /**
     * Validate offset pagination.
     *
     * @return array
     */
    protected function offset(): array
    {
        return array_merge(
            $this->restrictAllowedTypes(PagingParser::param(), collect([OffsetCriteria::NUMBER_PARAM, OffsetCriteria::SIZE_PARAM])),
            $this->min(Str::of(PagingParser::param())->append('.')->append(OffsetCriteria::NUMBER_PARAM)->__toString()),
            $this->range(Str::of(PagingParser::param())->append('.')->append(OffsetCriteria::SIZE_PARAM)->__toString()),
        );
    }

    /**
     * Validate limit pagination.
     *
     * @return array
     */
    protected function limit(): array
    {
        return array_merge(
            $this->restrictAllowedTypes(PagingParser::param(), collect([LimitCriteria::PARAM])),
            $this->range(Str::of(PagingParser::param())->append('.')->append(LimitCriteria::PARAM)->__toString()),
        );
    }

    /**
     * Validate minimum value for optional field.
     *
     * @param  string  $param
     * @param  int  $min
     * @return array
     */
    protected function min(string $param, int $min = 1): array
    {
        $minRule = Str::of('min:')->append($min)->__toString();

        return $this->optional($param, ['integer', $minRule]);
    }

    /**
     * Validate minimum and maximum value for optional field.
     *
     * @param  string  $param
     * @param  int  $min
     * @param  int  $max
     * @return array
     */
    protected function range(string $param, int $min = 1, int $max = Criteria::MAX_RESULTS): array
    {
        $minRule = Str::of('min:')->append($min)->__toString();
        $maxRule = Str::of('max:')->append($max)->__toString();

        return $this->optional($param, ['integer', $minRule, $maxRule]);
    }
}
