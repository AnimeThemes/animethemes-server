<?php

declare(strict_types=1);

namespace App\Policies\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Group;
use Illuminate\Auth\Access\HandlesAuthorization;
use Laravel\Nova\Nova;

/**
 * Class GroupPolicy.
 */
class GroupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  User|null  $user
     * @return bool
     */
    public function viewAny(?User $user): bool
    {
        return Nova::whenServing(
            fn (): bool => $user !== null && $user->can(CrudPermission::VIEW->format(Group::class)),
            fn (): bool => true
        );
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @return bool
     */
    public function view(?User $user): bool
    {
        return Nova::whenServing(
            fn (): bool => $user !== null && $user->can(CrudPermission::VIEW->format(Group::class)),
            fn (): bool => true
        );
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(Group::class));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Group  $group
     * @return bool
     */
    public function update(User $user, Group $group): bool
    {
        return ! $group->trashed() && $user->can(CrudPermission::UPDATE->format(Group::class));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Group  $group
     * @return bool
     */
    public function delete(User $user, Group $group): bool
    {
        return ! $group->trashed() && $user->can(CrudPermission::DELETE->format(Group::class));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  Group  $group
     * @return bool
     */
    public function restore(User $user, Group $group): bool
    {
        return $group->trashed() && $user->can(ExtendedCrudPermission::RESTORE->format(Group::class));
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::FORCE_DELETE->format(Group::class));
    }

    /**
     * Determine whether the user can add a theme to the group.
     *
     * @param  User  $user
     * @return bool
     */
    public function addAnimeTheme(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Group::class));
    }
}
