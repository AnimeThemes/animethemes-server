<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Announcement;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Admin\Announcement;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnnouncementForceDeleteTest.
 */
class AnnouncementForceDeleteTest extends TestCase
{
    /**
     * The Announcement Force Delete Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $announcement = Announcement::factory()->createOne();

        $response = $this->delete(route('api.announcement.forceDelete', ['announcement' => $announcement]));

        $response->assertUnauthorized();
    }

    /**
     * The Announcement Force Delete Endpoint shall forbid users without the force delete announcement permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $announcement = Announcement::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.announcement.forceDelete', ['announcement' => $announcement]));

        $response->assertForbidden();
    }

    /**
     * The Announcement Force Delete Endpoint shall force delete the announcement.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $announcement = Announcement::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(Announcement::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.announcement.forceDelete', ['announcement' => $announcement]));

        $response->assertOk();
        static::assertModelMissing($announcement);
    }
}
