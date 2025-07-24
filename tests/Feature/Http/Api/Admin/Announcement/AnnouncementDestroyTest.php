<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Announcement;

use App\Enums\Auth\CrudPermission;
use App\Models\Admin\Announcement;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AnnouncementDestroyTest extends TestCase
{
    /**
     * The Announcement Destroy Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $announcement = Announcement::factory()->createOne();

        $response = $this->delete(route('api.announcement.destroy', ['announcement' => $announcement]));

        $response->assertUnauthorized();
    }

    /**
     * The Announcement Destroy Endpoint shall forbid users without the delete announcement permission.
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
     * The Announcement Destroy Endpoint shall delete the announcement.
     */
    public function testDeleted(): void
    {
        $announcement = Announcement::factory()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Announcement::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.announcement.destroy', ['announcement' => $announcement]));

        $response->assertOk();
        static::assertModelMissing($announcement);
    }
}
