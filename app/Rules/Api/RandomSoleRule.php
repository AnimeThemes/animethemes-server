<?php

declare(strict_types=1);

namespace App\Rules\Api;

use App\Http\Api\Criteria\Sort\RandomCriteria;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Class RandomSoleRule.
 */
class RandomSoleRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $values = Str::of($value)->explode(',');

        if ($values->contains(RandomCriteria::PARAM_VALUE) && ! $values->containsOneItem()) {
            $fail(__('validation.api.random_sole'));
        }
    }
}
