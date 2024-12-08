<?php

declare(strict_types=1);

namespace App\Rules\Api;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Class DistinctIgnoringDirectionRule.
 */
class DistinctIgnoringDirectionRule implements ValidationRule
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

        $duplicateValues = $values->duplicates(function (mixed $sort) {
            /** @phpstan-ignore-next-line */
            if (is_string($sort) && Str::startsWith($sort, '-')) {
                return Str::replaceFirst('-', '', $sort);
            }

            return $sort;
        });

        if ($duplicateValues->isNotEmpty()) {
            $fail(__('validation.distinct'));
        }
    }
}
