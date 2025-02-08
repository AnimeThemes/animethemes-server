<?php

declare(strict_types=1);

namespace App\Events\Auth\User;

use App\Events\Base\Admin\AdminCreatedEvent;
use App\Models\Auth\User;

/**
 * Class UserCreated.
 *
 * @extends AdminCreatedEvent<User>
 */
class UserCreated extends AdminCreatedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  User  $user
     */
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return User
     */
    public function getModel(): User
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    protected function getDiscordMessageDescription(): string
    {
        return "User '**{$this->getModel()->getName()}**' has been created.";
    }
}
