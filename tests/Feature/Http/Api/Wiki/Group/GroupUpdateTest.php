<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Group;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Group;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class GroupUpdateTest.
 */
class GroupUpdateTest extends TestCase
{
    /**
     * The Group Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        $group = Group::factory()->createOne();

        $parameters = Group::factory()->raw();

        $response = $this->put(route('api.group.update', ['group' => $group] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Group Store Endpoint shall forbid users without the create group permission.
     *
     * @return void
     */
    public function test_forbidden(): void
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
     *
     * @return void
     */
    public function test_trashed(): void
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
     *
     * @return void
     */
    public function test_update(): void
    {
        $group = Group::factory()->createOne();

        $parameters = Group::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Group::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.group.update', ['group' => $group] + $parameters));

        $response->assertOk();
    }
}
