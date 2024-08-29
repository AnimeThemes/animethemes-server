<?php

declare(strict_types=1);

namespace App\Policies\List\External;

use App\Enums\Auth\Role as RoleEnum;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\List\External\ExternalToken;
use App\Policies\BasePolicy;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ExternalTokenPolicy.
 */
class ExternalTokenPolicy extends BasePolicy
{
    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  ExternalToken  $externaltoken
     * @return bool
     */
    public function update(User $user, BaseModel|Model $externaltoken): bool
    {
        if (Filament::isServing()) {
            return $user->hasRole(RoleEnum::ADMIN->value);
        }

        return parent::update($user, $externaltoken) && $user->getKey() === $externaltoken->user->getKey();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  ExternalToken  $externaltoken
     * @return bool
     */
    public function delete(User $user, BaseModel|Model $externaltoken): bool
    {
        if (Filament::isServing()) {
            return $user->hasRole(RoleEnum::ADMIN->value);
        }

        return parent::delete($user, $externaltoken) && $user->getKey() === $externaltoken->user->getKey();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  ExternalToken  $externaltoken
     * @return bool
     */
    public function restore(User $user, BaseModel|Model $externaltoken): bool
    {
        if (Filament::isServing()) {
            return $user->hasRole(RoleEnum::ADMIN->value);
        }

        return parent::restore($user, $externaltoken) && $user->getKey() === $externaltoken->user->getKey();
    }
}
