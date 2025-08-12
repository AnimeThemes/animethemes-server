<?php

declare(strict_types=1);

namespace App\GraphQL\Policies\List\External;

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\GraphQL\Policies\BasePolicy;
use App\Models\Auth\User;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use Illuminate\Support\Arr;

class ExternalEntryPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @param  array  $args
     */
    public function viewAny(?User $user, array $args = []): bool
    {
        /** @var ExternalProfile|null $profile */
        $profile = Arr::get($args, 'profile');

        if ($user !== null) {
            return ($profile?->user()->is($user) || $profile?->visibility !== ExternalProfileVisibility::PRIVATE)
                && $user->can(CrudPermission::VIEW->format(ExternalEntry::class));
        }

        return $profile?->visibility !== ExternalProfileVisibility::PRIVATE;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  array  $args
     */
    public function view(?User $user, array $args = [], ?string $keyName = 'model'): bool
    {
        /** @var ExternalProfile|null $profile */
        $profile = Arr::get($args, 'profile');

        if ($user !== null) {
            return ($profile?->user()->is($user) || $profile?->visibility !== ExternalProfileVisibility::PRIVATE)
                && $user->can(CrudPermission::VIEW->format(ExternalEntry::class));
        }

        return $profile?->visibility !== ExternalProfileVisibility::PRIVATE;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  array  $args
     */
    public function create(User $user, array $args = []): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  array  $args
     */
    public function update(User $user, array $args, ?string $keyName = 'model'): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  array  $args
     */
    public function delete(User $user, array $args, ?string $keyName = 'model'): bool
    {
        return false;
    }
}
