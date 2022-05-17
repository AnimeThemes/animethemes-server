<?php

declare(strict_types=1);

namespace App\Concerns\Http\Requests\Api;

use Illuminate\Contracts\Support\Arrayable;
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
     * @param  Arrayable<int, string>|String[]  $types
     * @return array<string, array>
     */
    protected function restrictAllowedTypes(string $param, Arrayable|array $types): array
    {
        if ($types instanceof Arrayable) {
            $types = $types->toArray();
        }

        return [
            $param => [
                'nullable',
                Str::of('array:')->append(implode(',', $types))->__toString(),
            ],
        ];
    }

    /**
     * Restrict the allowed values for the parameter.
     *
     * @param  string  $param
     * @param  Arrayable<int, string>|String[]|string  $values
     * @param  array  $customRules
     * @return array<string, array>
     */
    protected function restrictAllowedValues(string $param, Arrayable|array|string $values, array $customRules = []): array
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
     * @return array<string, array>
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
     * @return array<string, array>
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
     * @return array<string, array>
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
