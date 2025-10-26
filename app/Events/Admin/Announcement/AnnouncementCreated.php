<?php

declare(strict_types=1);

namespace App\Events\Admin\Announcement;

use App\Events\Base\Admin\AdminCreatedEvent;
use App\Models\Admin\Announcement;

/**
 * @extends AdminCreatedEvent<Announcement>
 */
class AnnouncementCreated extends AdminCreatedEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "Announcement '**{$this->getModel()->getName()}**' has been created.";
    }
}
