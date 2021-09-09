<?php

declare(strict_types=1);

namespace App\Http\Api\Parser;

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Sort\Criteria;
use App\Http\Api\Criteria\Sort\FieldCriteria;
use App\Http\Api\Criteria\Sort\RandomCriteria;
use App\Http\Api\Criteria\Sort\RelationCriteria;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class SortParser.
 */
class SortParser extends Parser
{
    /**
     * The parameter to parse.
     *
     * @var string|null
     */
    public static ?string $param = 'sort';

    /**
     * Parse sorts from parameters.
     *
     * @param  array  $parameters
     * @return Criteria[]
     */
    public static function parse(array $parameters): array
    {
        $criteria = [];

        if (Arr::exists($parameters, static::$param)) {
            $sortParam = $parameters[static::$param];
            if ($sortParam !== null && ! Arr::accessible($sortParam)) {
                $sortValues = Str::of($sortParam)->explode(',');
                foreach ($sortValues as $sortValue) {
                    $criteria[] = static::parseCriteria($sortValue);
                }
            }
        }

        return $criteria;
    }

    /**
     * Parse criteria instance from query string.
     *
     * @param  string  $sortValue
     * @return Criteria
     */
    protected static function parseCriteria(string $sortValue): Criteria
    {
        $field = Str::lower($sortValue);

        if ($field === RandomCriteria::PARAM_VALUE) {
            return new RandomCriteria();
        }

        $direction = Direction::ASCENDING();

        if (Str::startsWith($field, '-')) {
            $direction = Direction::DESCENDING();
            $field = Str::replaceFirst('-', '', $field);
        }

        if (Str::contains($field, '.')) {
            return new RelationCriteria($field, $direction);
        }

        return new FieldCriteria($field, $direction);
    }
}
