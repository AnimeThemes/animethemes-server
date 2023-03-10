<?php

declare(strict_types=1);

namespace App\Policies\Wiki\Anime;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Auth\Access\HandlesAuthorization;
use Laravel\Nova\Nova;

/**
 * Class AnimeSynonymPolicy.
 */
class AnimeSynonymPolicy
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
            fn (): bool => $user !== null && $user->can(CrudPermission::VIEW()->format(AnimeSynonym::class)),
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
            fn (): bool => $user !== null && $user->can(CrudPermission::VIEW()->format(AnimeSynonym::class)),
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
        return $user->can(CrudPermission::CREATE()->format(AnimeSynonym::class));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  AnimeSynonym  $animesynonym
     * @return bool
     */
    public function update(User $user, AnimeSynonym $animesynonym): bool
    {
        return ! $animesynonym->trashed() && $user->can(CrudPermission::UPDATE()->format(AnimeSynonym::class));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  AnimeSynonym  $animesynonym
     * @return bool
     */
    public function delete(User $user, AnimeSynonym $animesynonym): bool
    {
        return ! $animesynonym->trashed() && $user->can(CrudPermission::DELETE()->format(AnimeSynonym::class));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  AnimeSynonym  $animesynonym
     * @return bool
     */
    public function restore(User $user, AnimeSynonym $animesynonym): bool
    {
        return $animesynonym->trashed() && $user->can(ExtendedCrudPermission::RESTORE()->format(AnimeSynonym::class));
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::FORCE_DELETE()->format(AnimeSynonym::class));
    }
}
