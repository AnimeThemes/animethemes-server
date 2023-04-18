<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Billing\Balance;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Billing\Balance;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class BalanceRestoreTest.
 */
class BalanceRestoreTest extends TestCase
{
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
     * The Balance Restore Endpoint shall forbid users without the restore balance permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $balance = Balance::factory()->createOne();

        $balance->delete();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.balance.restore', ['balance' => $balance]));

        $response->assertForbidden();
    }

    /**
     * The Balance Restore Endpoint shall forbid users from restoring a balance that isn't trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $balance = Balance::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE()->format(Balance::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.balance.restore', ['balance' => $balance]));

        $response->assertForbidden();
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

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE()->format(Balance::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.balance.restore', ['balance' => $balance]));

        $response->assertOk();
        static::assertNotSoftDeleted($balance);
    }
}
