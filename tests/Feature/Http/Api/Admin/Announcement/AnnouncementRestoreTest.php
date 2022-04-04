<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Announcement;

use App\Models\Admin\Announcement;
use App\Models\Auth\User;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnnouncementRestoreTest.
 */
class AnnouncementRestoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Announcement Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $announcement = Announcement::factory()->createOne();

        $announcement->delete();

        $response = $this->patch(route('api.announcement.restore', ['announcement' => $announcement]));

        $response->assertUnauthorized();
    }

    /**
     * The Announcement Restore Endpoint shall restore the announcement.
     *
     * @return void
     */
    public function testRestored(): void
    {
        $announcement = Announcement::factory()->createOne();

        $announcement->delete();

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('admin')->createOne(),
            ['announcement:restore']
        );

        $response = $this->patch(route('api.announcement.restore', ['announcement' => $announcement]));

        $response->assertOk();
        static::assertNotSoftDeleted($announcement);
    }
}
