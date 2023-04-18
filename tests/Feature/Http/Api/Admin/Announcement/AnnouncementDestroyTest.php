<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Announcement;

use App\Enums\Auth\CrudPermission;
use App\Models\Admin\Announcement;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnnouncementDestroyTest.
 */
class AnnouncementDestroyTest extends TestCase
{
    /**
     * The Announcement Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $announcement = Announcement::factory()->createOne();

        $response = $this->delete(route('api.announcement.destroy', ['announcement' => $announcement]));

        $response->assertUnauthorized();
    }

    /**
     * The Announcement Destroy Endpoint shall forbid users without the delete announcement permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $announcement = Announcement::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.announcement.destroy', ['announcement' => $announcement]));

        $response->assertForbidden();
    }

    /**
     * The Announcement Destroy Endpoint shall forbid users from updating an announcement that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $announcement = Announcement::factory()->createOne();

        $announcement->delete();

        $user = User::factory()->withPermissions(CrudPermission::DELETE()->format(Announcement::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.announcement.destroy', ['announcement' => $announcement]));

        $response->assertNotFound();
    }

    /**
     * The Announcement Destroy Endpoint shall delete the announcement.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $announcement = Announcement::factory()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE()->format(Announcement::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.announcement.destroy', ['announcement' => $announcement]));

        $response->assertOk();
        static::assertSoftDeleted($announcement);
    }
}
