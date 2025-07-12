<?php

declare(strict_types=1);

namespace App\Concerns\Http\Requests\Api;

use App\Rules\Api\DelimitedRule;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Validation\Rule;

/**
 * Trait ValidatesParameters.
 */
trait ValidatesParameters
{
    /**
     * Restrict the allowed types for the parameter.
     *
     * @param  string  $param
     * @param  Arrayable<int, string>|array<int, string>  $types
     * @return array<string, array>
     */
    protected function restrictAllowedTypes(string $param, Arrayable|array $types): array
    {
        if ($types instanceof Arrayable) {
            $types = $types->toArray();
        }

        $types = implode(',', $types);

        return [
            $param => [
                'sometimes',
                'required',
                "array:$types",
            ],
        ];
    }

    /**
     * Restrict the allowed values for the parameter.
     *
     * @param  string  $param
     * @param  Arrayable<int, string>|array<int, string>|string  $values
     * @param  array  $customRules
     * @return array<string, array>
     */
    protected function restrictAllowedValues(string $param, Arrayable|array|string $values, array $customRules = []): array
    {
        return [
            $param => array_merge(
                [
                    'bail',
                    'sometimes',
                    'required',
                    'string',
                    new DelimitedRule(['required', Rule::in($values)->__toString()]),
                ],
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
