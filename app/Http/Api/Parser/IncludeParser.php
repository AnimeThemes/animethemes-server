<?php

declare(strict_types=1);

namespace App\Http\Api\Parser;

use App\Http\Api\Criteria\Include\Criteria;
use App\Http\Api\Criteria\Include\ResourceCriteria;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class IncludeParser extends Parser
{
    /**
     * The parameter to parse.
     */
    public static function param(): string
    {
        return 'include';
    }

    /**
     * Parse includes from parameters.
     *
     * @return Criteria[]
     */
    public static function parse(array $parameters): array
    {
        $criteria = [];

        if (Arr::exists($parameters, static::param())) {
            $includeParam = $parameters[static::param()];
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
     */
    protected static function parseCriteria(string $includeParam): Criteria
    {
        $paths = Str::of($includeParam)->lower()->explode(',');

        $paths = self::createIntermediatePaths($paths);

        return new Criteria($paths);
    }

    /**
     * Parse resource criteria instance from query string.
     */
    protected static function parseResourceCriteria(string $type, string $includeParam): ResourceCriteria
    {
        $paths = Str::of($includeParam)->lower()->explode(',');

        $paths = self::createIntermediatePaths($paths);

        return new ResourceCriteria(Str::lower($type), $paths);
    }

    /**
     * Create the intermediate paths as if they were in the URL.
     *
     * @param  Collection<int, string>  $paths
     * @return Collection<int, string>
     */
    protected static function createIntermediatePaths(Collection $paths): Collection
    {
        return $paths
            ->flatMap(function (string $path) {
                $pathParts = Str::of($path)->explode('.');

                return $pathParts->map(
                    fn (string $pathPart, int $index) => $pathParts
                        ->slice(0, $index + 1)
                        ->join('.'),
                );
            })
            ->unique();
    }
}
