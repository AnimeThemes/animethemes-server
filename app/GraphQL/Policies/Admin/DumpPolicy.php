<?php

declare(strict_types=1);

namespace App\GraphQL\Policies\Admin;

use App\Enums\Auth\Role;
use App\GraphQL\Policies\BasePolicy;
use App\Models\Admin\Dump;
use App\Models\Auth\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class DumpPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view the model.
     *
     * @param  array|null  $injected
     */
    public function view(?User $user, ?array $injected = null, ?string $keyName = 'id'): bool
    {
        if ($user?->hasRole(Role::ADMIN->value)) {
            return true;
        }

        /** @var Dump $dump */
        $dump = Arr::get($injected, $keyName);

        return Str::contains($dump->path, Dump::safeDumps());
    }
}
