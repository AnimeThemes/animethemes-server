<?php

namespace App\Events\Announcement;

use App\Models\Announcement;

abstract class AnnouncementEvent
{
    /**
     * The announcement that has fired this event.
     *
     * @var \App\Models\Announcement
     */
    protected $announcement;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Announcement $announcement
     * @return void
     */
    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    /**
     * Get the announcement that has fired this event.
     *
     * @return \App\Models\Announcement
     */
    public function getAnnouncement()
    {
        return $this->announcement;
    }
}
