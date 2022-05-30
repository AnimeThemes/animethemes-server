<?php

declare(strict_types=1);

namespace App\Rules\Api;

use App\Enums\BaseEnum;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class EnumDescriptionRule.
 */
class EnumDescriptionRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @param  class-string<BaseEnum>  $enumClass
     * @return void
     */
    public function __construct(protected readonly string $enumClass)
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return is_string($value) && $this->enumClass::fromDescription($value) !== null;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('validation.enum');
    }
}
