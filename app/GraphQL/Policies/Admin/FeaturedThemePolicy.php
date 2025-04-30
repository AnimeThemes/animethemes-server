<?php

declare(strict_types=1);

namespace App\GraphQL\Policies\Admin;

use App\GraphQL\Policies\BasePolicy;
use App\Models\Admin\FeaturedTheme;
use App\Models\Auth\User;
use Illuminate\Support\Arr;
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
     * @param  array|null  $injected
     * @return bool
     */
    public function view(?User $user, ?array $injected = null): bool
    {
        /** @var FeaturedTheme $featuredtheme */
        $featuredtheme = Arr::get($injected, 'id');

        return $featuredtheme->start_at->isBefore(Date::now());
    }
}
