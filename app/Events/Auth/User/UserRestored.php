<?php

declare(strict_types=1);

namespace App\Events\Auth\User;

use App\Events\Base\Admin\AdminRestoredEvent;
use App\Models\Auth\User;

/**
 * Class UserRestored.
 *
 * @extends AdminRestoredEvent<User>
 */
class UserRestored extends AdminRestoredEvent
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
        return "User '**{$this->getModel()->getName()}**' has been restored.";
    }
}
