<?php

declare(strict_types=1);

namespace App\Events\Auth\User;

use App\Contracts\Events\CascadesDeletesEvent;
use App\Events\Base\Admin\AdminDeletedEvent;
use App\Models\Auth\User;
use App\Models\List\ExternalProfile;

/**
 * @extends AdminDeletedEvent<User>
 */
class UserDeleted extends AdminDeletedEvent implements CascadesDeletesEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "User '**{$this->getModel()->getName()}**' has been deleted.";
    }

    public function cascadeDeletes(): void
    {
        $this->getModel()->externalprofiles->each(fn (ExternalProfile $profile) => $profile->delete());
    }
}
