<?php

declare(strict_types=1);

namespace App\Http\Api\Parser;

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Sort\Criteria;
use App\Http\Api\Criteria\Sort\FieldCriteria;
use App\Http\Api\Criteria\Sort\RandomCriteria;
use App\Http\Api\Criteria\Sort\RelationCriteria;
use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\Scope;
use App\Http\Api\Scope\ScopeParser;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SortParser extends Parser
{
    /**
     * The parameter to parse.
     */
    public static function param(): string
    {
        return 'sort';
    }

    /**
     * Parse sorts from parameters.
     *
     * @return Criteria[]
     */
    public static function parse(array $parameters): array
    {
        $criteria = [];

        if (Arr::exists($parameters, static::param())) {
            $sortParam = $parameters[static::param()];
            if ($sortParam !== null && ! Arr::accessible($sortParam)) {
                $sortValues = Str::of($sortParam)->explode(',');
                foreach ($sortValues as $sortValue) {
                    $criteria[] = static::parseCriteria(new GlobalScope(), $sortValue);
                }
            }
            if (Arr::accessible($sortParam) && Arr::isAssoc($sortParam)) {
                foreach ($sortParam as $type => $sortList) {
                    if ($sortList !== null && ! Arr::accessible($sortList)) {
                        $scope = ScopeParser::parse($type);
                        $sortValues = Str::of($sortList)->explode(',');
                        foreach ($sortValues as $sortValue) {
                            $criteria[] = static::parseCriteria($scope, $sortValue);
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
    protected static function parseCriteria(Scope $scope, string $sortValue): Criteria
    {
        $field = Str::lower($sortValue);

        if ($field === RandomCriteria::PARAM_VALUE) {
            return new RandomCriteria($scope);
        }

        $direction = Direction::ASCENDING;

        if (Str::startsWith($field, '-')) {
            $direction = Direction::DESCENDING;
            $field = Str::replaceFirst('-', '', $field);
        }

        if (Str::contains($field, '.')) {
            return new RelationCriteria($scope, $field, $direction);
        }

        return new FieldCriteria($scope, $field, $direction);
    }
}
