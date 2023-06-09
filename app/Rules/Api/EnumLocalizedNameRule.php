<?php

declare(strict_types=1);

namespace App\Rules\Api;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Class EnumLocalizedNameRule.
 */
readonly class EnumLocalizedNameRule implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param  class-string  $enumClass
     * @return void
     */
    public function __construct(protected string $enumClass)
    {
    }

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
        if (
            ! is_string($value)
            || ! enum_exists($this->enumClass)
            || ! method_exists($this->enumClass, 'fromLocalizedName')
            || $this->enumClass::fromLocalizedName($value) === null
        ) {
            $fail(__('validation.enum'));
        }
    }
}
