<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Announcement;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Admin\Announcement;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnnouncementRestoreTest.
 */
class AnnouncementRestoreTest extends TestCase
{
    /**
     * The Announcement Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $announcement = Announcement::factory()->trashed()->createOne();

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
        $announcement = Announcement::factory()->trashed()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.announcement.restore', ['announcement' => $announcement]));

        $response->assertForbidden();
    }

    /**
     * The Announcement Restore Endpoint shall forbid users from restoring an announcement that isn't trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $announcement = Announcement::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE()->format(Announcement::class))->createOne();

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
        $announcement = Announcement::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE()->format(Announcement::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.announcement.restore', ['announcement' => $announcement]));

        $response->assertOk();
        static::assertNotSoftDeleted($announcement);
    }
}
