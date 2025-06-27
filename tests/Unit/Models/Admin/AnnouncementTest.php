<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Admin;

use App\Models\Admin\Announcement;
use Tests\TestCase;

/**
 * Class AnnouncementTest.
 */
class AnnouncementTest extends TestCase
{
    /**
     * Announcements shall be nameable.
     *
     * @return void
     */
    public function test_nameable(): void
    {
        $announcement = Announcement::factory()->createOne();

        static::assertIsString($announcement->getName());
    }

    /**
     * Announcements shall have subtitle.
     *
     * @return void
     */
    public function test_has_subtitle(): void
    {
        $announcement = Announcement::factory()->createOne();

        static::assertIsString($announcement->getSubtitle());
    }
}
