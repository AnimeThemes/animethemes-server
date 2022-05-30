<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Announcement;

use App\Models\Admin\Announcement;
use App\Models\Auth\User;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnnouncementForceDeleteTest.
 */
class AnnouncementForceDeleteTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Announcement Force Destroy Endpoint shall be protected by sanctum.
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
     * The Announcement Force Destroy Endpoint shall force delete the announcement.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $announcement = Announcement::factory()->createOne();

        $user = User::factory()->withPermission('force delete announcement')->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.announcement.forceDelete', ['announcement' => $announcement]));

        $response->assertOk();
        static::assertModelMissing($announcement);
    }
}
