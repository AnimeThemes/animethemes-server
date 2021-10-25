<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Admin;

use App\Models\Admin\Announcement;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class AnnouncementTest.
 */
class AnnouncementTest extends TestCase
{

    /**
     * Announcement shall be auditable.
     *
     * @return void
     */
    public function testAuditable()
    {
        Config::set('audit.console', true);

        $announcement = Announcement::factory()->createOne();

        static::assertEquals(1, $announcement->audits()->count());
    }

    /**
     * Announcements shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $announcement = Announcement::factory()->createOne();

        static::assertIsString($announcement->getName());
    }
}
