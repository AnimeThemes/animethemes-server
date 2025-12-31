<?php

declare(strict_types=1);

namespace App\Events\Discord\DiscordThread;

use App\Events\Base\Admin\AdminDeletedEvent;
use App\Models\Discord\DiscordThread;

/**
 * @extends AdminDeletedEvent<DiscordThread>
 */
class DiscordThreadDeleted extends AdminDeletedEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "Discord Thread '**{$this->getModel()->getName()}**' has been deleted.";
    }
}
