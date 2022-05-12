<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Billing\Transaction;

use App\Models\Auth\User;
use App\Models\Billing\Transaction;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class TransactionDestroyTest.
 */
class TransactionDestroyTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Transaction Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $transaction = Transaction::factory()->createOne();

        $response = $this->delete(route('api.transaction.destroy', ['transaction' => $transaction]));

        $response->assertUnauthorized();
    }

    /**
     * The Transaction Destroy Endpoint shall delete the transaction.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $transaction = Transaction::factory()->createOne();

        $user = User::factory()->createOne();

        $user->givePermissionTo('delete transaction');

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.transaction.destroy', ['transaction' => $transaction]));

        $response->assertOk();
        static::assertSoftDeleted($transaction);
    }
}
