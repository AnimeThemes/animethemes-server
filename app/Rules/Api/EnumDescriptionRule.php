<?php

declare(strict_types=1);

namespace App\Rules\Api;

use App\Enums\BaseEnum;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Class EnumDescriptionRule.
 */
readonly class EnumDescriptionRule implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param  class-string<BaseEnum>  $enumClass
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
        if (! is_string($value) || $this->enumClass::fromDescription($value) === null) {
            $fail(__('validation.enum'));
        }
    }
}
