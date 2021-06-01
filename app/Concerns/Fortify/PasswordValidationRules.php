<?php

declare(strict_types=1);

namespace App\Concerns\Fortify;

use Illuminate\Validation\Rules\Password;

/**
 * Trait PasswordValidationRules.
 */
trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array
     */
    protected function passwordRules(): array
    {
        return array_merge(Password::required(), ['confirmed', 'zxcvbn_min:3', 'zxcvbn_dictionary']);
    }
}
