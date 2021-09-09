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
        $uniqueValues = collect($value)->unique(function ($sort) {
            if (Str::startsWith($sort, '-')) {
                return Str::replaceFirst('-', '', $sort);
            }

            return $sort;
        });

        return collect($value)->diff($uniqueValues)->isEmpty();
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
