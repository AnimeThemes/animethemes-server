<?php

declare(strict_types=1);

namespace App\Events\Auth\User;

use App\Events\Base\Admin\AdminUpdatedEvent;
use App\Models\Auth\User;

/**
 * @extends AdminUpdatedEvent<User>
 */
class UserUpdated extends AdminUpdatedEvent
{
    public function __construct(User $user)
    {
        parent::__construct($user);
        $this->initializeEmbedFields($user);
    }

    protected function getDiscordMessageDescription(): string
    {
        return "User '**{$this->getModel()->getName()}**' has been updated.";
    }
}
