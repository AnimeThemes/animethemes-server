<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Admin;

use App\Models\Admin\Announcement;
use Tests\TestCase;

class AnnouncementTest extends TestCase
{
    /**
     * Announcements shall be nameable.
     */
    public function testNameable(): void
    {
        $announcement = Announcement::factory()->createOne();

        static::assertIsString($announcement->getName());
    }

    /**
     * Announcements shall have subtitle.
     */
    public function testHasSubtitle(): void
    {
        $announcement = Announcement::factory()->createOne();

        static::assertIsString($announcement->getSubtitle());
    }
}
