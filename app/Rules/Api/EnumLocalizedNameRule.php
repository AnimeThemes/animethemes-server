<?php

declare(strict_types=1);

namespace App\Rules\Api;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

readonly class EnumLocalizedNameRule implements ValidationRule
{
    /**
     * @param  class-string  $enumClass
     */
    public function __construct(protected string $enumClass) {}

    /**
     * @param  Closure(string): PotentiallyTranslatedString  $fail
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
