<?php

declare(strict_types=1);

namespace App\Http\Api\Parser;

use App\Http\Api\Criteria\Filter\Criteria;
use App\Http\Api\Criteria\Filter\HasCriteria;
use App\Http\Api\Criteria\Filter\TrashedCriteria;
use App\Http\Api\Criteria\Filter\WhereCriteria;
use App\Http\Api\Criteria\Filter\WhereInCriteria;
use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\Scope;
use App\Http\Api\Scope\ScopeParser;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FilterParser extends Parser
{
    /**
     * The parameter to parse.
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
                foreach ($filterParam as $scopeOrType => $filterList) {
                    if ($filterList !== null && ! Arr::accessible($filterList)) {
                        $criteria[] = static::parseCriteria(new GlobalScope(), $scopeOrType, $filterList);
                    }
                    if (Arr::accessible($filterList) && Arr::isAssoc($filterList)) {
                        $scope = ScopeParser::parse($scopeOrType);
                        foreach ($filterList as $filter => $filterValues) {
                            if ($filterValues !== null && ! Arr::accessible($filterValues)) {
                                $criteria[] = static::parseCriteria($scope, $filter, $filterValues);
                            }
                        }
                    }
                }
            }
        }

        return $criteria;
    }

    /**
     * Parse criteria instance from query string.
     */
    protected static function parseCriteria(Scope $scope, string $filterParam, mixed $filterValues): Criteria
    {
        $param = Str::lower($filterParam);

        if (Str::of($param)->explode(Criteria::PARAM_SEPARATOR)->contains(TrashedCriteria::PARAM_VALUE)) {
            return TrashedCriteria::make($scope, $param, $filterValues);
        }

        if (Str::of($param)->explode(Criteria::PARAM_SEPARATOR)->contains(HasCriteria::PARAM_VALUE)) {
            return HasCriteria::make($scope, $filterParam, $filterValues);
        }

        if (Str::contains($filterValues, Criteria::VALUE_SEPARATOR)) {
            return WhereInCriteria::make($scope, $param, $filterValues);
        }

        return WhereCriteria::make($scope, $param, $filterValues);
    }
}
