<?php

declare(strict_types=1);

namespace App\Policies\Billing;

use App\Models\Auth\User;
use App\Models\Billing\Transaction;
use Illuminate\Auth\Access\HandlesAuthorization;
use Laravel\Nova\Nova;

/**
 * Class TransactionPolicy.
 */
class TransactionPolicy
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
            fn (): bool => $user !== null && $user->can('view transaction'),
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
            fn (): bool => $user !== null && $user->can('view transaction'),
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
        return $user->can('create transaction');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Transaction  $transaction
     * @return bool
     */
    public function update(User $user, Transaction $transaction): bool
    {
        return ! $transaction->trashed() && $user->can('update transaction');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Transaction  $transaction
     * @return bool
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        return ! $transaction->trashed() && $user->can('delete transaction');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  Transaction  $transaction
     * @return bool
     */
    public function restore(User $user, Transaction $transaction): bool
    {
        return $transaction->trashed() && $user->can('restore transaction');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->can('force delete transaction');
    }
}
