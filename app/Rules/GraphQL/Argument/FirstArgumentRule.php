<?php

declare(strict_types=1);

namespace App\Rules\GraphQL\Argument;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Config;
use Illuminate\Translation\PotentiallyTranslatedString;

readonly class FirstArgumentRule implements ValidationRule
{
    /**
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $maxCount = Config::get('graphql.pagination_values.max_count');
        $maxCount = 10;

        if ($maxCount !== null && $value > $maxCount) {
            $fail(__('validation.max.numeric', ['max' => $maxCount]));
        }
    }
}
