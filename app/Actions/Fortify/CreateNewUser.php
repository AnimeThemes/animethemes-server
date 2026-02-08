<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use App\Models\Auth\User;
use App\Rules\ModerationRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            User::ATTRIBUTE_NAME => ['required', 'string', 'max:35', 'alpha_dash', Rule::unique(User::class), new ModerationRule()],
            User::ATTRIBUTE_EMAIL => ['required', 'string', 'email', 'max:255', 'indisposable', Rule::unique(User::class)],
            User::ATTRIBUTE_PASSWORD => Password::required(),
            'terms' => ['required'],
        ])->validate();

        $user = new User([
            User::ATTRIBUTE_NAME => Arr::get($input, User::ATTRIBUTE_NAME),
            User::ATTRIBUTE_EMAIL => Arr::get($input, User::ATTRIBUTE_EMAIL),
            User::ATTRIBUTE_PASSWORD => Hash::make(Arr::get($input, User::ATTRIBUTE_PASSWORD)),
        ]);

        $user->save();

        return $user;
    }
}
