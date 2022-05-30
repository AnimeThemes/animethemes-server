<?php

declare(strict_types=1);

namespace App\Events\Admin\Announcement;

use App\Events\Base\Admin\AdminUpdatedEvent;
use App\Models\Admin\Announcement;

/**
 * Class AnnouncementUpdated.
 *
 * @extends AdminUpdatedEvent<Announcement>
 */
class AnnouncementUpdated extends AdminUpdatedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Announcement  $announcement
     */
    public function __construct(Announcement $announcement)
    {
        parent::__construct($announcement);
        $this->initializeEmbedFields($announcement);
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
        return "Announcement '**{$this->getModel()->getName()}**' has been updated.";
    }
}
