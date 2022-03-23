<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Announcement;

use App\Models\Admin\Announcement;
use App\Models\Auth\User;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnnouncementDestroyTest.
 */
class AnnouncementDestroyTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Announcement Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $announcement = Announcement::factory()->createOne();

        $response = $this->delete(route('api.announcement.destroy', ['announcement' => $announcement]));

        $response->assertForbidden();
    }

    /**
     * The Announcement Destroy Endpoint shall delete the announcement.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $announcement = Announcement::factory()->createOne();

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('admin')->createOne(),
            ['announcement:delete']
        );

        $response = $this->delete(route('api.announcement.destroy', ['announcement' => $announcement]));

        $response->assertOk();
        static::assertSoftDeleted($announcement);
    }
}
