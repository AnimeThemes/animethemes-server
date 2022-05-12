<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Billing\Transaction;

use App\Models\Auth\User;
use App\Models\Billing\Transaction;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class TransactionRestoreTest.
 */
class TransactionRestoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Transaction Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $transaction = Transaction::factory()->createOne();

        $transaction->delete();

        $response = $this->patch(route('api.transaction.restore', ['transaction' => $transaction]));

        $response->assertUnauthorized();
    }

    /**
     * The Transaction Restore Endpoint shall restore the transaction.
     *
     * @return void
     */
    public function testRestored(): void
    {
        $transaction = Transaction::factory()->createOne();

        $transaction->delete();

        $user = User::factory()->withPermission('restore transaction')->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.transaction.restore', ['transaction' => $transaction]));

        $response->assertOk();
        static::assertNotSoftDeleted($transaction);
    }
}
