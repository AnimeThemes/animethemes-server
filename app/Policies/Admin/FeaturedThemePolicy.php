<?php

declare(strict_types=1);

namespace App\Policies\Admin;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Admin\FeaturedTheme;
use App\Models\Auth\User;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Date;

/**
 * Class FeaturedThemePolicy.
 */
class FeaturedThemePolicy
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
        if (Filament::isServing()) {
            return $user !== null && $user->can(CrudPermission::VIEW->format(FeaturedTheme::class));
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @param  FeaturedTheme  $featuredtheme
     * @return bool
     */
    public function view(?User $user, FeaturedTheme $featuredtheme): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->can(CrudPermission::VIEW->format(FeaturedTheme::class));
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(FeaturedTheme::class));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  FeaturedTheme  $featuredtheme
     * @return bool
     */
    public function update(User $user, FeaturedTheme $featuredtheme): bool
    {
        return ! $featuredtheme->trashed() && $user->can(CrudPermission::UPDATE->format(FeaturedTheme::class));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  FeaturedTheme  $featuredtheme
     * @return bool
     */
    public function delete(User $user, FeaturedTheme $featuredtheme): bool
    {
        return ! $featuredtheme->trashed() && $user->can(CrudPermission::DELETE->format(FeaturedTheme::class));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  FeaturedTheme  $featuredtheme
     * @return bool
     */
    public function restore(User $user, FeaturedTheme $featuredtheme): bool
    {
        return $featuredtheme->trashed() && $user->can(ExtendedCrudPermission::RESTORE->format(FeaturedTheme::class));
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::FORCE_DELETE->format(FeaturedTheme::class));
    }

    /**
     * Determine whether the user can permanently delete any model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::FORCE_DELETE->format(FeaturedTheme::class));
    }
}
