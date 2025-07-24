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
     * Determine whether the user can view the model.
     *
     * @param  array|null  $injected
     */
    public function view(?User $user, ?array $injected = null, ?string $keyName = 'id'): bool
    {
        /** @var Feature $feature */
        $feature = Arr::get($injected, $keyName);

        return $feature->isNullScope() && parent::view($user, $injected, $keyName);
    }
}
