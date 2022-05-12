<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Billing\Balance;

use App\Models\Auth\User;
use App\Models\Billing\Balance;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class BalanceRestoreTest.
 */
class BalanceRestoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Balance Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $balance = Balance::factory()->createOne();

        $balance->delete();

        $response = $this->patch(route('api.balance.restore', ['balance' => $balance]));

        $response->assertUnauthorized();
    }

    /**
     * The Balance Restore Endpoint shall restore the balance.
     *
     * @return void
     */
    public function testRestored(): void
    {
        $balance = Balance::factory()->createOne();

        $balance->delete();

        $user = User::factory()->createOne();

        $user->givePermissionTo('restore balance');

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.balance.restore', ['balance' => $balance]));

        $response->assertOk();
        static::assertNotSoftDeleted($balance);
    }
}
