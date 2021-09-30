<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use App\Concerns\Actions\Fortify\PasswordValidationRules;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator as IlluminateValidator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

/**
 * Class UpdateUserPassword.
 */
class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and update the user's password.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     * @throws ValidationException
     */
    public function update($user, array $input)
    {
        Validator::make($input, [
            'current_password' => ['required', 'string'],
            'password' => $this->passwordRules(),
        ])->after(function (IlluminateValidator $validator) use ($user, $input) {
            if (! isset($input['current_password']) || ! Hash::check($input['current_password'], $user->password)) {
                $validator->errors()
                    ->add('current_password', __('The provided password does not match your current password.'));
            }
        })->validateWithBag('updatePassword');

        $user->forceFill([
            User::ATTRIBUTE_PASSWORD => Hash::make($input['password']),
        ])->save();
    }
}
