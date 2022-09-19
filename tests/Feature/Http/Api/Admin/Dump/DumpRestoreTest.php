<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Dump;

use App\Models\Admin\Dump;
use App\Models\Auth\User;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class DumpRestoreTest.
 */
class DumpRestoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Dump Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $dump = Dump::factory()->createOne();

        $dump->delete();

        $response = $this->patch(route('api.dump.restore', ['dump' => $dump]));

        $response->assertUnauthorized();
    }

    /**
     * The Dump Restore Endpoint shall restore the dump.
     *
     * @return void
     */
    public function testRestored(): void
    {
        $dump = Dump::factory()->createOne();

        $dump->delete();

        $user = User::factory()->withPermission('restore dump')->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.dump.restore', ['dump' => $dump]));

        $response->assertOk();
        static::assertNotSoftDeleted($dump);
    }
}
