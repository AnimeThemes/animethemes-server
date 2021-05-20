<?php

namespace App\Concerns\Fortify;

use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array
     */
    protected function passwordRules()
    {
        return array_merge(Password::required(), ['confirmed', 'zxcvbn_min:3', 'zxcvbn_dictionary']);
    }
}
