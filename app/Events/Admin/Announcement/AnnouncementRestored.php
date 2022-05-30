<?php

declare(strict_types=1);

namespace App\Events\Admin\Announcement;

use App\Events\Base\Admin\AdminRestoredEvent;
use App\Models\Admin\Announcement;

/**
 * Class AnnouncementRestored.
 *
 * @extends AdminRestoredEvent<Announcement>
 */
class AnnouncementRestored extends AdminRestoredEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Announcement  $announcement
     */
    public function __construct(Announcement $announcement)
    {
        parent::__construct($announcement);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Announcement
     */
    public function getModel(): Announcement
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
        return "Announcement '**{$this->getModel()->getName()}**' has been restored.";
    }
}
