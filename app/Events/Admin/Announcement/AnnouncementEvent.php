<?php

declare(strict_types=1);

namespace App\Events\Admin\Announcement;

use App\Models\Admin\Announcement;

/**
 * Class AnnouncementEvent.
 */
abstract class AnnouncementEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Announcement  $announcement
     * @return void
     */
    public function __construct(protected Announcement $announcement) {}

    /**
     * Get the announcement that has fired this event.
     *
     * @return Announcement
     */
    public function getAnnouncement(): Announcement
    {
        return $this->announcement;
    }
}
