<?php

namespace App\Http\Controllers\Auth;

class NovaResetPasswordController extends \Laravel\Nova\Http\Controllers\ResetPasswordController
{

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'string', 'min:8', 'zxcvbn_min:3', 'zxcvbn_dictionary'],
        ];
    }
}
