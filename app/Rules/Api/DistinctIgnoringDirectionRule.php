<?php

declare(strict_types=1);

namespace App\Rules\Api;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

/**
 * Class DistinctIgnoringDirectionRule.
 */
class DistinctIgnoringDirectionRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $values = Str::of($value)->explode(',');

        $uniqueValues = $values->unique(function ($sort) {
            if (Str::startsWith($sort, '-')) {
                return Str::replaceFirst('-', '', $sort);
            }

            return $sort;
        });

        return $values->diff($uniqueValues)->isEmpty();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('validation.distinct');
    }
}
