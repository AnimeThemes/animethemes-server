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
     * The user that has fired this event.
     *
     * @var User
     */
    protected User $user;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
