<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Dump;

use App\Models\Admin\Dump;
use App\Models\Auth\User;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class DumpForceDeleteTest.
 */
class DumpForceDeleteTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Dump Force Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $dump = Dump::factory()->createOne();

        $response = $this->delete(route('api.dump.forceDelete', ['dump' => $dump]));

        $response->assertUnauthorized();
    }

    /**
     * The Dump Force Destroy Endpoint shall force delete the dump.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $dump = Dump::factory()->createOne();

        $user = User::factory()->withPermission('force delete dump')->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.dump.forceDelete', ['dump' => $dump]));

        $response->assertOk();
        static::assertModelMissing($dump);
    }
}
