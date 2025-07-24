<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Announcement;

use App\Enums\Auth\CrudPermission;
use App\Models\Admin\Announcement;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AnnouncementUpdateTest extends TestCase
{
    /**
     * The Announcement Update Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $announcement = Announcement::factory()->createOne();

        $parameters = Announcement::factory()->raw();

        $response = $this->put(route('api.announcement.update', ['announcement' => $announcement] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Announcement Update Endpoint shall forbid users without the update announcement permission.
     */
    public function testForbidden(): void
    {
        $announcement = Announcement::factory()->createOne();

        $parameters = Announcement::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.announcement.update', ['announcement' => $announcement] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Announcement Update Endpoint shall update an announcement.
     */
    public function testUpdate(): void
    {
        $announcement = Announcement::factory()->createOne();

        $parameters = Announcement::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Announcement::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.announcement.update', ['announcement' => $announcement] + $parameters));

        $response->assertOk();
    }
}
