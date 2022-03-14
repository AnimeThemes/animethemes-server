<?php

declare(strict_types=1);

namespace App\Concerns\Http\Requests\Api;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\ValidationRules\Rules\Delimited;

/**
 * Trait ValidatesParameters.
 */
trait ValidatesParameters
{
    /**
     * Restrict the allowed types for the parameter.
     *
     * @param  string  $param
     * @param  Collection  $types
     * @return array
     */
    protected function restrictAllowedTypes(string $param, Collection $types): array
    {
        return [
            $param => [
                'nullable',
                Str::of('array:')->append($types->join(','))->__toString(),
            ],
        ];
    }

    /**
     * Restrict the allowed values for the parameter.
     *
     * @param  string  $param
     * @param  Collection  $values
     * @param  array  $customRules
     * @return array
     */
    protected function restrictAllowedValues(string $param, Collection $values, array $customRules = []): array
    {
        return [
            $param => array_merge(
                ['bail', 'sometimes', 'required', 'string', new Delimited(Rule::in($values))],
                $customRules,
            ),
        ];
    }

    /**
     * Prohibit the parameter.
     *
     * @param  string  $param
     * @return array
     */
    protected function prohibit(string $param): array
    {
        return [
            $param => [
                'prohibited',
            ],
        ];
    }

    /**
     * Optional parameter.
     *
     * @param  string  $param
     * @param  array  $customRules
     * @return array
     */
    protected function optional(string $param, array $customRules = []): array
    {
        return [
            $param => array_merge(
                ['sometimes', 'required'],
                $customRules,
            ),
        ];
    }

    /**
     * Require the parameter.
     *
     * @param  string  $param
     * @param  array  $customRules
     * @return array
     */
    protected function require(string $param, array $customRules = []): array
    {
        return [
            $param => array_merge(
                ['required'],
                $customRules,
            ),
        ];
    }
}
