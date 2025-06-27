<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Group;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Group;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class GroupStoreTest.
 */
class GroupStoreTest extends TestCase
{
    /**
     * The Group Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        $group = Group::factory()->makeOne();

        $response = $this->post(route('api.group.store', $group->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Group Store Endpoint shall forbid users without the create group permission.
     *
     * @return void
     */
    public function test_forbidden(): void
    {
        $group = Group::factory()->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.group.store', $group->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Group Store Endpoint shall create a group.
     *
     * @return void
     */
    public function test_create(): void
    {
        $parameters = Group::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Group::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.group.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(Group::class, 1);
    }
}
