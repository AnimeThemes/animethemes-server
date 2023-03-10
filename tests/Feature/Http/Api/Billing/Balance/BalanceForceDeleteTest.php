<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Billing\Balance;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Billing\Balance;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class BalanceForceDeleteTest.
 */
class BalanceForceDeleteTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Balance Force Delete Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $balance = Balance::factory()->createOne();

        $response = $this->delete(route('api.balance.forceDelete', ['balance' => $balance]));

        $response->assertUnauthorized();
    }

    /**
     * The Balance Force Delete Endpoint shall forbid users without the force delete balance permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $balance = Balance::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.balance.forceDelete', ['balance' => $balance]));

        $response->assertForbidden();
    }

    /**
     * The Balance Force Delete Endpoint shall force delete the balance.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $balance = Balance::factory()->createOne();

        $user = User::factory()->withPermission(ExtendedCrudPermission::FORCE_DELETE()->format(Balance::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.balance.forceDelete', ['balance' => $balance]));

        $response->assertOk();
        static::assertModelMissing($balance);
    }
}
