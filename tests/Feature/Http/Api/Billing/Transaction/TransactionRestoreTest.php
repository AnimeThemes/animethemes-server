<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Billing\Transaction;

use App\Enums\Auth\ExtendedCrudPermission;
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
     * The Transaction Restore Endpoint shall forbid users without the restore transaction permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $transaction = Transaction::factory()->createOne();

        $transaction->delete();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.transaction.restore', ['transaction' => $transaction]));

        $response->assertForbidden();
    }

    /**
     * The Transaction Restore Endpoint shall forbid users from restoring a transaction that isn't trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $transaction = Transaction::factory()->createOne();

        $user = User::factory()->withPermission(ExtendedCrudPermission::RESTORE()->format(Transaction::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.transaction.restore', ['transaction' => $transaction]));

        $response->assertForbidden();
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

        $user = User::factory()->withPermission(ExtendedCrudPermission::RESTORE()->format(Transaction::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.transaction.restore', ['transaction' => $transaction]));

        $response->assertOk();
        static::assertNotSoftDeleted($transaction);
    }
}
