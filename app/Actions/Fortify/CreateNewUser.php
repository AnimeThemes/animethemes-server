<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use App\Concerns\Actions\Fortify\PasswordValidationRules;
use App\Models\Auth\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

/**
 * Class CreateNewUser.
 */
class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return User
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required'] : '',
        ])->validate();

        return User::factory()->createOne([
            'name' => Arr::get($input, 'name'),
            'email' => Arr::get($input, 'email'),
            'email_verified_at' => null,
            'password' => Hash::make(Arr::get($input, 'password')),
            'remember_token' => null,
        ]);
    }
}
