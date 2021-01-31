<?php

namespace App\Events\User;

use App\Models\User;

abstract class UserEvent
{
    /**
     * The user that has fired this event.
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the user that has fired this event.
     *
     * @return \App\Models\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
