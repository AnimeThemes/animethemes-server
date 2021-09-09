<?php

declare(strict_types=1);

namespace App\Http\Api\Parser;

use App\Http\Api\Criteria\Include\Criteria;
use App\Http\Api\Criteria\Include\ResourceCriteria;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class IncludeParser.
 */
class IncludeParser extends Parser
{
    /**
     * The parameter to parse.
     *
     * @var string|null
     */
    public static ?string $param = 'include';

    /**
     * Parse includes from parameters.
     *
     * @param  array  $parameters
     * @return Criteria[]
     */
    public static function parse(array $parameters): array
    {
        $criteria = [];

        if (Arr::exists($parameters, static::$param)) {
            $includeParam = $parameters[static::$param];
            if ($includeParam !== null && ! Arr::accessible($includeParam)) {
                $criteria[] = static::parseCriteria($includeParam);
            }
            if (Arr::accessible($includeParam) && Arr::isAssoc($includeParam)) {
                foreach ($includeParam as $type => $includeList) {
                    if ($includeList !== null && ! Arr::accessible($includeList)) {
                        $criteria[] = static::parseResourceCriteria($type, $includeList);
                    }
                }
            }
        }

        return $criteria;
    }

    /**
     * Parse criteria instance from query string.
     *
     * @param  string  $includeParam
     * @return Criteria
     */
    protected static function parseCriteria(string $includeParam): Criteria
    {
        $paths = Str::of($includeParam)->lower()->explode(',');

        return new Criteria($paths);
    }

    /**
     * Parse resource criteria instance from query string.
     *
     * @param  string  $type
     * @param  string  $includeParam
     * @return ResourceCriteria
     */
    protected static function parseResourceCriteria(string $type, string $includeParam): ResourceCriteria
    {
        $paths = Str::of($includeParam)->lower()->explode(',');

        return new ResourceCriteria(Str::lower($type), $paths);
    }
}
