<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Announcement;

use App\Enums\Auth\CrudPermission;
use App\Models\Admin\Announcement;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnnouncementStoreTest.
 */
class AnnouncementStoreTest extends TestCase
{
    /**
     * The Announcement Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $announcement = Announcement::factory()->makeOne();

        $response = $this->post(route('api.announcement.store', $announcement->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Announcement Store Endpoint shall forbid users without the create announcement permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $announcement = Announcement::factory()->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.announcement.store', $announcement->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Announcement Store Endpoint shall require the content field.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(Announcement::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.announcement.store'));

        $response->assertJsonValidationErrors([
            Announcement::ATTRIBUTE_CONTENT,
        ]);
    }

    /**
     * The Announcement Store Endpoint shall create an announcement.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $parameters = Announcement::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(Announcement::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.announcement.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(Announcement::TABLE, 1);
    }
}
