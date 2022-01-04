<?php

declare(strict_types=1);

namespace App\Events\Auth\User;

use App\Models\Auth\User;

/**
 * Class UserEvent.
 */
abstract class UserEvent
{
    /**
     * Create a new event instance.
     *
     * @param  User  $user
     * @return void
     */
    public function __construct(protected User $user)
    {
    }

    /**
     * Get the user that has fired this event.
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
