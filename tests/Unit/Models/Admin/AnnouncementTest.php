<?php

declare(strict_types=1);

namespace Models\Admin;

use App\Models\Admin\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class AnnouncementTest.
 */
class AnnouncementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Announcement shall be auditable.
     *
     * @return void
     */
    public function testAuditable()
    {
        Config::set('audit.console', true);

        $announcement = Announcement::factory()->create();

        static::assertEquals(1, $announcement->audits->count());
    }

    /**
     * Announcements shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $announcement = Announcement::factory()->create();

        static::assertIsString($announcement->getName());
    }
}
