<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Dump;

use App\Enums\Auth\CrudPermission;
use App\Models\Admin\Dump;
use App\Models\Auth\User;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class DumpDestroyTest.
 */
class DumpDestroyTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Dump Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $dump = Dump::factory()->createOne();

        $response = $this->delete(route('api.dump.destroy', ['dump' => $dump]));

        $response->assertUnauthorized();
    }

    /**
     * The Dump Destroy Endpoint shall forbid users without the delete dump permission.
     *
     * @return void
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
     * The Dump Destroy Endpoint shall forbid users from updating a dump that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $dump = Dump::factory()->createOne();

        $dump->delete();

        $user = User::factory()->withPermission(CrudPermission::DELETE()->format(Dump::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.dump.destroy', ['dump' => $dump]));

        $response->assertNotFound();
    }

    /**
     * The Dump Destroy Endpoint shall delete the dump.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $dump = Dump::factory()->createOne();

        $user = User::factory()->withPermission(CrudPermission::DELETE()->format(Dump::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.dump.destroy', ['dump' => $dump]));

        $response->assertOk();
        static::assertSoftDeleted($dump);
    }
}
