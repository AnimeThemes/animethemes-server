<?php

declare(strict_types=1);

namespace App\Policies\Admin;

use App\Enums\Auth\CrudPermission;
use App\Models\Admin\Feature;
use App\Models\Auth\User;
use App\Policies\BasePolicy;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class FeaturePolicy extends BasePolicy
{
    /**
     * @param  Feature  $feature
     */
    public function view(?User $user, Model $feature): Response
    {
        if (Filament::isServing()) {
            return $user?->can(CrudPermission::VIEW->format(Feature::class))
                ? Response::allow()
                : Response::deny();
        }

        /** @phpstan-ignore-next-line */
        return parent::view($user, $feature) && $feature->isNullScope()
            ? Response::allow()
            : Response::deny();
    }
}
