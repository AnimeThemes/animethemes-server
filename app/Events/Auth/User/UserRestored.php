<?php

declare(strict_types=1);

namespace App\Events\Auth\User;

use App\Events\Base\Admin\AdminRestoredEvent;
use App\Models\Auth\User;

/**
 * @extends AdminRestoredEvent<User>
 */
class UserRestored extends AdminRestoredEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "User '**{$this->getModel()->getName()}**' has been restored.";
    }
}
