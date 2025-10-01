<?php

declare(strict_types=1);

namespace App\Rules\Api;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;

class DistinctIgnoringDirectionRule implements ValidationRule
{
    /**
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $values = Str::of($value)->explode(',');

        $duplicateValues = $values->duplicates(function (mixed $sort) {
            /** @phpstan-ignore-next-line */
            if (Str::startsWith($sort, '-')) {
                return Str::replaceFirst('-', '', $sort);
            }

            return $sort;
        });

        if ($duplicateValues->isNotEmpty()) {
            $fail(__('validation.distinct'));
        }
    }
}
