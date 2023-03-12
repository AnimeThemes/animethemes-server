<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Billing\Transaction;

use App\Enums\Auth\CrudPermission;
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
     * The Transaction Destroy Endpoint shall forbid users without the delete transaction permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $transaction = Transaction::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.transaction.destroy', ['transaction' => $transaction]));

        $response->assertForbidden();
    }

    /**
     * The Transaction Destroy Endpoint shall forbid users from updating a transaction that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $transaction = Transaction::factory()->createOne();

        $transaction->delete();

        $user = User::factory()->withPermissions(CrudPermission::DELETE()->format(Transaction::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.transaction.destroy', ['transaction' => $transaction]));

        $response->assertNotFound();
    }

    /**
     * The Transaction Destroy Endpoint shall delete the transaction.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $transaction = Transaction::factory()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE()->format(Transaction::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.transaction.destroy', ['transaction' => $transaction]));

        $response->assertOk();
        static::assertSoftDeleted($transaction);
    }
}
