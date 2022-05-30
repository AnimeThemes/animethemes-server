<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Announcement;

use App\Models\Admin\Announcement;
use App\Models\Auth\User;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnnouncementUpdateTest.
 */
class AnnouncementUpdateTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Announcement Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $announcement = Announcement::factory()->createOne();

        $parameters = Announcement::factory()->raw();

        $response = $this->put(route('api.announcement.update', ['announcement' => $announcement] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Announcement Update Endpoint shall update an announcement.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $announcement = Announcement::factory()->createOne();

        $parameters = Announcement::factory()->raw();

        $user = User::factory()->withPermission('update announcement')->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.announcement.update', ['announcement' => $announcement] + $parameters));

        $response->assertOk();
    }
}
