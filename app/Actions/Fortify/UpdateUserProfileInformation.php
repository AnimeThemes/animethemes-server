<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use App\Models\Auth\User;
use App\Rules\ModerationRule;
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
            User::ATTRIBUTE_NAME => ['required_without:'.User::ATTRIBUTE_EMAIL, 'string', 'max:255', 'alpha_dash', Rule::unique(User::class)->ignore($user->id), new ModerationRule()],
            User::ATTRIBUTE_EMAIL => ['required_without:'.User::ATTRIBUTE_NAME, 'string', 'email', 'max:255', 'indisposable', Rule::unique(User::class)->ignore($user->id)],
        ])->validate();

        $email = Arr::get($validated, User::ATTRIBUTE_EMAIL);
        if (Arr::has($validated, User::ATTRIBUTE_EMAIL) && $email !== $user->email) {
            $validated = $validated + [User::ATTRIBUTE_EMAIL_VERIFIED_AT => null];

            $user->update($validated);

            $user->sendEmailVerificationNotification();
        } else {
            $user->update($validated);
        }
    }
}
