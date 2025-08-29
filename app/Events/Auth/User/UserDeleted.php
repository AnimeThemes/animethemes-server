<?php

declare(strict_types=1);

namespace App\Events\Auth\User;

use App\Events\Base\Admin\AdminDeletedEvent;
use App\Models\Auth\User;

/**
 * @extends AdminDeletedEvent<User>
 */
class UserDeleted extends AdminDeletedEvent
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function getModel(): User
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "User '**{$this->getModel()->getName()}**' has been deleted.";
    }
}
