<?php

declare(strict_types=1);

namespace App\Http\Api\Parser;

use App\Http\Api\Criteria\Field\Criteria;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class FieldParser.
 */
class FieldParser extends Parser
{
    /**
     * The parameter to parse.
     *
     * @return string
     */
    public static function param(): string
    {
        return 'fields';
    }

    /**
     * Parse sparse fieldsets from parameters.
     *
     * @param  array  $parameters
     * @return Criteria[]
     */
    public static function parse(array $parameters): array
    {
        $criteria = [];

        if (Arr::exists($parameters, static::param())) {
            $fieldsParam = $parameters[static::param()];
            if (Arr::accessible($fieldsParam) && Arr::isAssoc($fieldsParam)) {
                foreach ($fieldsParam as $type => $fieldList) {
                    if ($fieldList !== null && ! Arr::accessible($fieldList)) {
                        $criteria[] = static::parseCriteria($type, $fieldList);
                    }
                }
            }
        }

        return $criteria;
    }

    /**
     * Parse criteria instance from query string.
     *
     * @param  string  $type
     * @param  string  $fieldList
     * @return Criteria
     */
    protected static function parseCriteria(string $type, string $fieldList): Criteria
    {
        $fields = Str::of($fieldList)->lower()->explode(',');

        return new Criteria(Str::lower($type), $fields);
    }
}
