<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Billing\Transaction;

use App\Models\Auth\User;
use App\Models\Billing\Transaction;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class TransactionForceDeleteTest.
 */
class TransactionForceDeleteTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Transaction Force Destroy Endpoint shall be protected by sanctum.
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
     * The Transaction Force Destroy Endpoint shall force delete the transaction.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $transaction = Transaction::factory()->createOne();

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('admin')->createOne(),
            ['*']
        );

        $response = $this->delete(route('api.transaction.forceDelete', ['transaction' => $transaction]));

        $response->assertOk();
        static::assertModelMissing($transaction);
    }
}
