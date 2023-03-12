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
 * Class DumpStoreTest.
 */
class DumpStoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Dump Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $dump = Dump::factory()->makeOne();

        $response = $this->post(route('api.dump.store', $dump->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Dump Store Endpoint shall forbid users without the create dump permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $dump = Dump::factory()->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.dump.store', $dump->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Dump Store Endpoint shall require the path field.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(Dump::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.dump.store'));

        $response->assertJsonValidationErrors([
            Dump::ATTRIBUTE_PATH,
        ]);
    }

    /**
     * The Dump Store Endpoint shall create a dump.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $parameters = Dump::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(Dump::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.dump.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(Dump::TABLE, 1);
    }
}
