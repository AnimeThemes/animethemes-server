<?php

declare(strict_types=1);

namespace App\Http\Api\Parser;

use App\Http\Api\Criteria\Search\Criteria;
use Illuminate\Support\Arr;

class SearchParser extends Parser
{
    /**
     * The parameter to parse.
     */
    public static function param(): string
    {
        return 'q';
    }

    /**
     * Parse search from parameters.
     *
     * @param  array  $parameters
     * @return Criteria[]
     */
    public static function parse(array $parameters): array
    {
        $criteria = [];

        if (Arr::exists($parameters, static::param())) {
            $searchParam = $parameters[static::param()];
            if ($searchParam !== null && ! Arr::accessible($searchParam)) {
                $criteria[] = static::parseCriteria($searchParam);
            }
        }

        return $criteria;
    }

    /**
     * Parse criteria instance from query string.
     */
    protected static function parseCriteria(string $searchParam): Criteria
    {
        return new Criteria($searchParam);
    }
}
