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
     * @param  array  $args
     */
    public function view(?User $user, array $args = [], ?string $keyName = 'model'): bool
    {
        /** @var FeaturedTheme $featuredtheme */
        $featuredtheme = Arr::get($args, $keyName);

        return $featuredtheme->start_at->isBefore(Date::now()) && parent::view($user, $args, $keyName);
    }
}
