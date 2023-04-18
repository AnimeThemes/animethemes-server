<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Dump;

use App\Enums\Auth\CrudPermission;
use App\Models\Admin\Dump;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class DumpUpdateTest.
 */
class DumpUpdateTest extends TestCase
{
    /**
     * The Dump Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $dump = Dump::factory()->createOne();

        $parameters = Dump::factory()->raw();

        $response = $this->put(route('api.dump.update', ['dump' => $dump] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Dump Update Endpoint shall forbid users without the update dump permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $dump = Dump::factory()->createOne();

        $parameters = Dump::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.dump.update', ['dump' => $dump] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Dump Update Endpoint shall forbid users from updating a dump that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $dump = Dump::factory()->createOne();

        $dump->delete();

        $parameters = Dump::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(Dump::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.dump.update', ['dump' => $dump] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Dump Update Endpoint shall update a dump.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $dump = Dump::factory()->createOne();

        $parameters = Dump::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(Dump::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.dump.update', ['dump' => $dump] + $parameters));

        $response->assertOk();
    }
}
