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
     * @param  array  $args
     */
    public function view(?User $user, array $args = [], ?string $keyName = 'model'): bool
    {
        if ($user?->hasRole(Role::ADMIN->value)) {
            return true;
        }

        /** @var Dump $dump */
        $dump = Arr::get($args, $keyName);

        return Str::contains($dump->path, Dump::safeDumps());
    }
}
