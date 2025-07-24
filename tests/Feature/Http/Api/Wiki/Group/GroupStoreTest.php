<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Group;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Group;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GroupStoreTest extends TestCase
{
    /**
     * The Group Store Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $group = Group::factory()->makeOne();

        $response = $this->post(route('api.group.store', $group->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Group Store Endpoint shall forbid users without the create group permission.
     */
    public function testForbidden(): void
    {
        $group = Group::factory()->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.group.store', $group->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Group Store Endpoint shall create a group.
     */
    public function testCreate(): void
    {
        $parameters = Group::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Group::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.group.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(Group::class, 1);
    }
}
