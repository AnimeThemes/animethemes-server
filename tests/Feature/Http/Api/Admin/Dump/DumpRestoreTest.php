<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Dump;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Admin\Dump;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class DumpRestoreTest.
 */
class DumpRestoreTest extends TestCase
{
    /**
     * The Dump Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        $dump = Dump::factory()->trashed()->createOne();

        $response = $this->patch(route('api.dump.restore', ['dump' => $dump]));

        $response->assertUnauthorized();
    }

    /**
     * The Dump Restore Endpoint shall forbid users without the restore dump permission.
     *
     * @return void
     */
    public function test_forbidden(): void
    {
        $dump = Dump::factory()->trashed()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.dump.restore', ['dump' => $dump]));

        $response->assertForbidden();
    }

    /**
     * The Dump Restore Endpoint shall forbid users from restoring a dump that isn't trashed.
     *
     * @return void
     */
    public function test_trashed(): void
    {
        $dump = Dump::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Dump::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.dump.restore', ['dump' => $dump]));

        $response->assertForbidden();
    }

    /**
     * The Dump Restore Endpoint shall restore the dump.
     *
     * @return void
     */
    public function test_restored(): void
    {
        $dump = Dump::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Dump::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.dump.restore', ['dump' => $dump]));

        $response->assertOk();
        static::assertNotSoftDeleted($dump);
    }
}
