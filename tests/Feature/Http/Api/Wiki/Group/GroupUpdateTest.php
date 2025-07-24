<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Group;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Group;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GroupUpdateTest extends TestCase
{
    /**
     * The Group Update Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $group = Group::factory()->createOne();

        $parameters = Group::factory()->raw();

        $response = $this->put(route('api.group.update', ['group' => $group] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Group Store Endpoint shall forbid users without the create group permission.
     */
    public function testForbidden(): void
    {
        $group = Group::factory()->createOne();

        $parameters = Group::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.group.update', ['group' => $group] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Group Update Endpoint shall forbid users from updating a group that is trashed.
     */
    public function testTrashed(): void
    {
        $group = Group::factory()->trashed()->createOne();

        $parameters = Group::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Group::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.group.update', ['group' => $group] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Group Update Endpoint shall update a group.
     */
    public function testUpdate(): void
    {
        $group = Group::factory()->createOne();

        $parameters = Group::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Group::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.group.update', ['group' => $group] + $parameters));

        $response->assertOk();
    }
}
