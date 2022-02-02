<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use App\Models\Auth\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

/**
 * Class CreateNewUser.
 */
class CreateNewUser implements CreatesNewUsers
{
    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return User
     *
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::TABLE)],
            'password' => Password::required(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required'] : '',
        ])->validate();

        return User::factory()->createOne([
            User::ATTRIBUTE_NAME => Arr::get($input, 'name'),
            User::ATTRIBUTE_EMAIL => Arr::get($input, 'email'),
            User::ATTRIBUTE_EMAIL_VERIFIED_AT => null,
            User::ATTRIBUTE_PASSWORD => Hash::make(Arr::get($input, 'password')),
            User::ATTRIBUTE_REMEMBER_TOKEN => null,
        ]);
    }
}
