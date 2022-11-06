<?php

declare(strict_types=1);

namespace App\Policies\Billing;

use App\Models\Auth\User;
use App\Models\Billing\Balance;
use Illuminate\Auth\Access\HandlesAuthorization;
use Laravel\Nova\Nova;

/**
 * Class BalancePolicy.
 */
class BalancePolicy
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
            fn (): bool => $user !== null && $user->can('view balance'),
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
            fn (): bool => $user !== null && $user->can('view balance'),
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
        return $user->can('create balance');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Balance  $balance
     * @return bool
     */
    public function update(User $user, Balance $balance): bool
    {
        return ! $balance->trashed() && $user->can('update balance');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Balance  $balance
     * @return bool
     */
    public function delete(User $user, Balance $balance): bool
    {
        return ! $balance->trashed() && $user->can('delete balance');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  Balance  $balance
     * @return bool
     */
    public function restore(User $user, Balance $balance): bool
    {
        return $balance->trashed() && $user->can('restore balance');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->can('force delete balance');
    }
}
