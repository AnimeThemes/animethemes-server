<?php

namespace Tests\Unit\Models;

use App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

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

        $this->assertEquals(1, $announcement->audits->count());
    }

    /**
     * Announcements shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $announcement = Announcement::factory()->create();

        $this->assertIsString($announcement->getName());
    }
}
