<?php

declare(strict_types=1);

namespace App\Events\Admin\Announcement;

use App\Events\Base\Admin\AdminUpdatedEvent;
use App\Models\Admin\Announcement;

/**
 * @extends AdminUpdatedEvent<Announcement>
 */
class AnnouncementUpdated extends AdminUpdatedEvent
{
    public function __construct(Announcement $announcement)
    {
        parent::__construct($announcement);
        $this->initializeEmbedFields($announcement);
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Announcement '**{$this->getModel()->getName()}**' has been updated.";
    }
}
