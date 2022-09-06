<?php

declare(strict_types=1);

namespace App\Rules\Api;

use App\Http\Api\Criteria\Sort\RandomCriteria;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

/**
 * Class RandomSoleRule.
 */
class RandomSoleRule implements Rule
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

        return ! $values->contains(RandomCriteria::PARAM_VALUE) || $values->containsOneItem();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('validation.api.random_sole');
    }
}
