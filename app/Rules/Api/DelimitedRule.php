<?php

declare(strict_types=1);

namespace App\Rules\Api;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;

readonly class DelimitedRule implements ValidationRule
{
    /**
     * @param  string|array|ValidationRule  $rule
     */
    public function __construct(protected string|array|ValidationRule $rule) {}

    /**
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $key = Str::of($attribute)->explode('.')->last();
        $items = Str::of($value)->explode(',');

        if ($items->unique()->count() !== $items->count()) {
            $fail(__('validation.api.unique'));

            return;
        }

        foreach ($items as $item) {
            $validator = Validator::make(
                [$key => $item],
                [$key => $this->rule]
            );

            if ($validator->fails()) {
                $fail($validator->getMessageBag()->first($key));

                return;
            }
        }
    }
}
