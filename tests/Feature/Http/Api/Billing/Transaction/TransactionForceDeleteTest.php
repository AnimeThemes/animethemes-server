<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Billing\Transaction;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Billing\Transaction;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class TransactionForceDeleteTest.
 */
class TransactionForceDeleteTest extends TestCase
{
    /**
     * The Transaction Force Delete Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $transaction = Transaction::factory()->createOne();

        $response = $this->delete(route('api.transaction.forceDelete', ['transaction' => $transaction]));

        $response->assertUnauthorized();
    }

    /**
     * The Transaction Force Delete Endpoint shall forbid users without the force delete transaction permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $transaction = Transaction::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.transaction.forceDelete', ['transaction' => $transaction]));

        $response->assertForbidden();
    }

    /**
     * The Transaction Force Delete Endpoint shall force delete the transaction.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $transaction = Transaction::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE()->format(Transaction::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.transaction.forceDelete', ['transaction' => $transaction]));

        $response->assertOk();
        static::assertModelMissing($transaction);
    }
}
