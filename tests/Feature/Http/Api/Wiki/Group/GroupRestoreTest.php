<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Group;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Group;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class GroupRestoreTest.
 */
class GroupRestoreTest extends TestCase
{
    /**
     * The Group Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $group = Group::factory()->trashed()->createOne();

        $response = $this->patch(route('api.group.restore', ['group' => $group]));

        $response->assertUnauthorized();
    }

    /**
     * The Group Restore Endpoint shall forbid users without the restore group permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $group = Group::factory()->trashed()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.group.restore', ['group' => $group]));

        $response->assertForbidden();
    }

    /**
     * The Group Restore Endpoint shall forbid users from restoring a group that isn't trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $group = Group::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Group::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.group.restore', ['group' => $group]));

        $response->assertForbidden();
    }

    /**
     * The Group Restore Endpoint shall restore the group.
     *
     * @return void
     */
    public function testRestored(): void
    {
        $group = Group::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Group::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.group.restore', ['group' => $group]));

        $response->assertOk();
        static::assertNotSoftDeleted($group);
    }
}
