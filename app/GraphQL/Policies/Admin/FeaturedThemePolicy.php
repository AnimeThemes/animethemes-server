<?php

declare(strict_types=1);

namespace App\GraphQL\Policies\Admin;

use App\GraphQL\Policies\BasePolicy;
use App\Models\Admin\FeaturedTheme;
use App\Models\Auth\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;

class FeaturedThemePolicy extends BasePolicy
{
    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @param  array|null  $injected
     * @param  string|null  $keyName
     * @return bool
     */
    public function view(?User $user, ?array $injected = null, ?string $keyName = 'id'): bool
    {
        /** @var FeaturedTheme $featuredtheme */
        $featuredtheme = Arr::get($injected, $keyName);

        return $featuredtheme->start_at->isBefore(Date::now()) && parent::view($user, $injected, $keyName);
    }
}
