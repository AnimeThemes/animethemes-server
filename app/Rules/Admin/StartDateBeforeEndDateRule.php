<?php

declare(strict_types=1);

namespace App\Rules\Admin;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;
use Illuminate\Translation\PotentiallyTranslatedString;
use RuntimeException;

/**
 * Class StartDateBeforeEndDateRule.
 */
class StartDateBeforeEndDateRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     * @return void
     *
     * @throws RuntimeException
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $dates = explode(' - ', $value);

        $startAt = Carbon::createFromFormat('m/d/Y', $dates[0]);
        $endAt = Carbon::createFromFormat('m/d/Y', $dates[1]);

        if ($endAt->lessThan($startAt)) {
            $fail(__('validation.date', ['attribute' => $attribute]));
        }
    }
}
