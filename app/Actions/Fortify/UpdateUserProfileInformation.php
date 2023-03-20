<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use App\Models\Auth\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

/**
 * Class UpdateUserProfileInformation.
 */
class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  User  $user
     * @param  array<string, string>  $input
     * @return void
     *
     * @throws ValidationException
     */
    public function update(User $user, array $input): void
    {
        $validated = Validator::make($input, [
            User::ATTRIBUTE_NAME => ['sometimes', 'required', 'string', 'max:255', 'alpha_dash', Rule::unique(User::TABLE)->ignore($user->id)],
            User::ATTRIBUTE_EMAIL => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique(User::TABLE)->ignore($user->id)],
        ])->validate();

        $email = Arr::get($validated, User::ATTRIBUTE_EMAIL);
        if ($email !== $user->email) {
            $validated = $validated + [User::ATTRIBUTE_EMAIL_VERIFIED_AT => null];

            $user->update($validated);

            $user->sendEmailVerificationNotification();
        } else {
            $user->update($validated);
        }
    }
}
