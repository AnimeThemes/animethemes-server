<?php

declare(strict_types=1);

namespace App\Rules\Api;

use Illuminate\Contracts\Validation\Rule;

/**
 * Class IsValidBoolean.
 */
class IsValidBoolean implements Rule
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
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('validation.boolean');
    }
}
