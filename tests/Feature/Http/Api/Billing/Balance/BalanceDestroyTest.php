<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Billing\Balance;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Billing\Balance;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class BalanceDestroyTest.
 */
class BalanceDestroyTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Balance Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $balance = Balance::factory()->createOne();

        $response = $this->delete(route('api.balance.destroy', ['balance' => $balance]));

        $response->assertUnauthorized();
    }

    /**
     * The Balance Destroy Endpoint shall forbid users without the delete balance permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $balance = Balance::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.balance.destroy', ['balance' => $balance]));

        $response->assertForbidden();
    }

    /**
     * The Balance Destroy Endpoint shall forbid users from updating a balance that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $balance = Balance::factory()->createOne();

        $balance->delete();

        $user = User::factory()->withPermissions(CrudPermission::DELETE()->format(Balance::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.balance.destroy', ['balance' => $balance]));

        $response->assertNotFound();
    }

    /**
     * The Balance Destroy Endpoint shall delete the balance.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $balance = Balance::factory()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE()->format(Balance::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.balance.destroy', ['balance' => $balance]));

        $response->assertOk();
        static::assertSoftDeleted($balance);
    }
}
