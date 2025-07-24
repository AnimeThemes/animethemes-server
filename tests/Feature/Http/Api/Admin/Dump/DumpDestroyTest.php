<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Dump;

use App\Enums\Auth\CrudPermission;
use App\Models\Admin\Dump;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DumpDestroyTest extends TestCase
{
    /**
     * The Dump Destroy Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $dump = Dump::factory()->createOne();

        $response = $this->delete(route('api.dump.destroy', ['dump' => $dump]));

        $response->assertUnauthorized();
    }

    /**
     * The Dump Destroy Endpoint shall forbid users without the delete dump permission.
     */
    public function testForbidden(): void
    {
        $dump = Dump::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.dump.destroy', ['dump' => $dump]));

        $response->assertForbidden();
    }

    /**
     * The Dump Destroy Endpoint shall delete the dump.
     */
    public function testDeleted(): void
    {
        $dump = Dump::factory()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Dump::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.dump.destroy', ['dump' => $dump]));

        $response->assertOk();
        static::assertModelMissing($dump);
    }
}
