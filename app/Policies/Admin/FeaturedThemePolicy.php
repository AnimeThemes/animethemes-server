<?php

declare(strict_types=1);

namespace App\Policies\Admin;

use App\Enums\Auth\CrudPermission;
use App\Models\Admin\FeaturedTheme;
use App\Models\Auth\User;
use App\Policies\BasePolicy;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class FeaturedThemePolicy extends BasePolicy
{
    /**
     * @param  FeaturedTheme  $featuredtheme
     */
    public function view(?User $user, Model $featuredtheme): Response
    {
        if (Filament::isServing()) {
            return $user?->can(CrudPermission::VIEW->format(FeaturedTheme::class))
                ? Response::allow()
                : Response::deny();
        }

        /** @phpstan-ignore-next-line */
        return parent::view($user, $featuredtheme) && $featuredtheme->start_at->isPast()
            ? Response::allow()
            : Response::deny();
    }
}
