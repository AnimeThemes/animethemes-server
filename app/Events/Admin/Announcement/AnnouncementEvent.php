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
     * The announcement that has fired this event.
     *
     * @var Announcement
     */
    protected Announcement $announcement;

    /**
     * Create a new event instance.
     *
     * @param Announcement $announcement
     * @return void
     */
    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

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