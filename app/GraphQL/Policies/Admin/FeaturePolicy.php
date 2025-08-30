<?php

declare(strict_types=1);

namespace App\GraphQL\Policies\Admin;

use App\GraphQL\Policies\BasePolicy;
use App\Models\Admin\Feature;
use App\Models\Auth\User;
use Illuminate\Support\Arr;

class FeaturePolicy extends BasePolicy
{
    /**
     * @param  array  $args
     */
    public function view(?User $user, array $args = [], ?string $keyName = 'model'): bool
    {
        /** @var Feature $feature */
        $feature = Arr::get($args, $keyName);

        return $feature->isNullScope() && parent::view($user, $args, $keyName);
    }
}
