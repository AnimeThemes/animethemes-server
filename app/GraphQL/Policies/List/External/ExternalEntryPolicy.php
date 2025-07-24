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
     * @param  array|null  $injected
     */
    public function viewAny(?User $user, ?array $injected = null): bool
    {
        /** @var ExternalProfile|null $profile */
        $profile = Arr::get($injected, 'profile');

        if ($user !== null) {
            return ($profile?->user()->is($user) || $profile?->visibility !== ExternalProfileVisibility::PRIVATE)
                && $user->can(CrudPermission::VIEW->format(ExternalEntry::class));
        }

        return $profile?->visibility !== ExternalProfileVisibility::PRIVATE;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  array|null  $injected
     */
    public function view(?User $user, ?array $injected = null, ?string $keyName = 'id'): bool
    {
        /** @var ExternalProfile|null $profile */
        $profile = Arr::get($injected, 'profile');

        if ($user !== null) {
            return ($profile?->user()->is($user) || $profile?->visibility !== ExternalProfileVisibility::PRIVATE)
                && $user->can(CrudPermission::VIEW->format(ExternalEntry::class));
        }

        return $profile?->visibility !== ExternalProfileVisibility::PRIVATE;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  array|null  $injected
     */
    public function create(User $user, ?array $injected = null): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  array  $injected
     */
    public function update(User $user, array $injected, ?string $keyName = 'id'): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  array  $injected
     */
    public function delete(User $user, array $injected, ?string $keyName = 'id'): bool
    {
        return false;
    }
}
