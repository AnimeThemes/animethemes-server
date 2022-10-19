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
     * The Announcement Restore Endpoint shall forbid users without the restore announcement permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $announcement = Announcement::factory()->createOne();

        $announcement->delete();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.announcement.restore', ['announcement' => $announcement]));

        $response->assertForbidden();
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

        $user = User::factory()->withPermission('restore announcement')->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.announcement.restore', ['announcement' => $announcement]));

        $response->assertOk();
        static::assertNotSoftDeleted($announcement);
    }
}
