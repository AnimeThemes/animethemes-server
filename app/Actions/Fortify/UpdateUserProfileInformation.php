<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use App\Models\Auth\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
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
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     *
     * @throws ValidationException
     */
    public function update(mixed $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255', Rule::unique(User::TABLE)->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique(User::TABLE)->ignore($user->id)],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if ($input['email'] !== $user->email && $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                User::ATTRIBUTE_NAME => Arr::get($input, 'name'),
                User::ATTRIBUTE_EMAIL => Arr::get($input, 'email'),
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    protected function updateVerifiedUser(mixed $user, array $input): void
    {
        $user->forceFill([
            User::ATTRIBUTE_NAME => Arr::get($input, 'name'),
            User::ATTRIBUTE_EMAIL => Arr::get($input, 'email'),
            User::ATTRIBUTE_EMAIL_VERIFIED_AT => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
