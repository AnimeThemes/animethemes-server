<?php

declare(strict_types=1);

namespace App\Listeners\Auth;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use Illuminate\Auth\Events\Verified;

/**
 * Class AssignDefaultRolesToVerifiedUser.
 */
class AssignDefaultRolesToVerifiedUser
{
    /**
     * Handle the event.
     *
     * @param  Verified  $event
     * @return void
     */
    public function handle(Verified $event): void
    {
        /** @var User $user */
        $user = $event->user;

        $defaultRoles = Role::query()
            ->where(Role::ATTRIBUTE_DEFAULT, true)
            ->get();

        if ($defaultRoles->isNotEmpty()) {
            $user->assignRole($defaultRoles);
        }
    }
}
