<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Group;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Group;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class GroupDestroyTest.
 */
class GroupDestroyTest extends TestCase
{
    /**
     * The Group Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        $group = Group::factory()->createOne();

        $response = $this->delete(route('api.group.destroy', ['group' => $group]));

        $response->assertUnauthorized();
    }

    /**
     * The Group Destroy Endpoint shall forbid users without the delete group permission.
     *
     * @return void
     */
    public function test_forbidden(): void
    {
        $group = Group::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.group.destroy', ['group' => $group]));

        $response->assertForbidden();
    }

    /**
     * The Group Destroy Endpoint shall forbid users from updating a group that is trashed.
     *
     * @return void
     */
    public function test_trashed(): void
    {
        $group = Group::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Group::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.group.destroy', ['group' => $group]));

        $response->assertNotFound();
    }

    /**
     * The Group Destroy Endpoint shall delete the group.
     *
     * @return void
     */
    public function test_deleted(): void
    {
        $group = Group::factory()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Group::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.group.destroy', ['group' => $group]));

        $response->assertOk();
        static::assertSoftDeleted($group);
    }
}
