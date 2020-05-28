<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ConfirmTwoFactorAuthRule implements Rule
{

    private $user;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->user->confirmTwoFactorAuth($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('nova.2fa_invalid_code');
    }
}
