<?php

declare(strict_types=1);

namespace App\Http\Api\Parser;

use App\Http\Api\Criteria\Filter\Criteria;
use App\Http\Api\Criteria\Filter\HasCriteria;
use App\Http\Api\Criteria\Filter\TrashedCriteria;
use App\Http\Api\Criteria\Filter\WhereCriteria;
use App\Http\Api\Criteria\Filter\WhereInCriteria;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class FilterParser.
 */
class FilterParser extends Parser
{
    /**
     * The parameter to parse.
     *
     * @return string
     */
    public static function param(): string
    {
        return 'filter';
    }

    /**
     * Parse filter criteria from parameters.
     *
     * @param  array  $parameters
     * @return Criteria[]
     */
    public static function parse(array $parameters): array
    {
        $criteria = [];

        if (Arr::exists($parameters, static::param())) {
            $filterParam = $parameters[static::param()];
            if (Arr::accessible($filterParam) && Arr::isAssoc($filterParam)) {
                foreach (Arr::dot($filterParam) as $filterCriteria => $filterValues) {
                    if ($filterValues !== null) {
                        $criteria[] = static::parseCriteria($filterCriteria, $filterValues);
                    }
                }
            }
        }

        return $criteria;
    }

    /**
     * Parse criteria instance from query string.
     *
     * @param  string  $filterParam
     * @param  mixed  $filterValues
     * @return Criteria
     */
    protected static function parseCriteria(string $filterParam, mixed $filterValues): Criteria
    {
        $param = Str::lower($filterParam);

        if (Str::of($param)->explode('.')->contains(TrashedCriteria::PARAM_VALUE)) {
            return TrashedCriteria::make($param, $filterValues);
        }

        if (Str::of($param)->explode('.')->contains(HasCriteria::PARAM_VALUE)) {
            return HasCriteria::make($filterParam, $filterValues);
        }

        if (Str::contains($filterValues, ',')) {
            return WhereInCriteria::make($param, $filterValues);
        }

        return WhereCriteria::make($param, $filterValues);
    }
}
