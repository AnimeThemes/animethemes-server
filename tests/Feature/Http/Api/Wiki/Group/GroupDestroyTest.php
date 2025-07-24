<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Group;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Group;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GroupDestroyTest extends TestCase
{
    /**
     * The Group Destroy Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $group = Group::factory()->createOne();

        $response = $this->delete(route('api.group.destroy', ['group' => $group]));

        $response->assertUnauthorized();
    }

    /**
     * The Group Destroy Endpoint shall forbid users without the delete group permission.
     */
    public function testForbidden(): void
    {
        $group = Group::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.group.destroy', ['group' => $group]));

        $response->assertForbidden();
    }

    /**
     * The Group Destroy Endpoint shall forbid users from updating a group that is trashed.
     */
    public function testTrashed(): void
    {
        $group = Group::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Group::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.group.destroy', ['group' => $group]));

        $response->assertNotFound();
    }

    /**
     * The Group Destroy Endpoint shall delete the group.
     */
    public function testDeleted(): void
    {
        $group = Group::factory()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Group::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.group.destroy', ['group' => $group]));

        $response->assertOk();
        static::assertSoftDeleted($group);
    }
}
