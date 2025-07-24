<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Group;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Group;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GroupForceDeleteTest extends TestCase
{
    /**
     * The Group Force Delete Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $group = Group::factory()->createOne();

        $response = $this->delete(route('api.group.forceDelete', ['group' => $group]));

        $response->assertUnauthorized();
    }

    /**
     * The Group Force Delete Endpoint shall forbid users without the force delete group permission.
     */
    public function testForbidden(): void
    {
        $group = Group::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.group.forceDelete', ['group' => $group]));

        $response->assertForbidden();
    }

    /**
     * The Group Force Delete Endpoint shall force delete the group.
     */
    public function testDeleted(): void
    {
        $group = Group::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(Group::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.group.forceDelete', ['group' => $group]));

        $response->assertOk();
        static::assertModelMissing($group);
    }
}
