<?php

declare(strict_types=1);

namespace App\Policies\Admin;

use App\Enums\Auth\CrudPermission;
use App\Models\Admin\FeaturedTheme;
use App\Models\Auth\User;
use App\Policies\BasePolicy;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;

/**
 * Class FeaturedThemePolicy.
 */
class FeaturedThemePolicy extends BasePolicy
{
    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @param  FeaturedTheme  $featuredtheme
     * @return bool
     */
    public function view(?User $user, Model $featuredtheme): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->can(CrudPermission::VIEW->format(FeaturedTheme::class));
        }

        return $featuredtheme->start_at->isBefore(Date::now());
    }
}
