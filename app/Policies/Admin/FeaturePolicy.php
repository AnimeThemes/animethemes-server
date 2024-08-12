<?php

declare(strict_types=1);

namespace App\Policies\Admin;

use App\Enums\Auth\CrudPermission;
use App\Models\Admin\Feature;
use App\Models\Auth\User;
use App\Policies\BasePolicy;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FeaturePolicy.
 */
class FeaturePolicy extends BasePolicy
{
    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @param  Feature  $feature
     * @return bool
     */
    public function view(?User $user, Model $feature): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->can(CrudPermission::VIEW->format(Feature::class));
        }

        return $feature->isNullScope();
    }
}
