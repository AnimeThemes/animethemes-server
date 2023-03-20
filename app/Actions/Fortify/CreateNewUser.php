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

/**
 * Class CreateNewUser.
 */
class CreateNewUser implements CreatesNewUsers
{
    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     * @return User
     *
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique(User::TABLE)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::TABLE)],
            'password' => Password::required(),
            'terms' => ['required'],
        ])->validate();

        $user = new User([
            User::ATTRIBUTE_NAME => Arr::get($input, 'name'),
            User::ATTRIBUTE_EMAIL => Arr::get($input, 'email'),
            User::ATTRIBUTE_PASSWORD => Hash::make(Arr::get($input, 'password')),
        ]);

        $user->save();

        return $user;
    }
}
