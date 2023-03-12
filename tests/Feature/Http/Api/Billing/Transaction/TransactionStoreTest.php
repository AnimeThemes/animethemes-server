<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Billing\Transaction;

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Billing\Service;
use App\Models\Auth\User;
use App\Models\Billing\Transaction;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class TransactionStoreTest.
 */
class TransactionStoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Transaction Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $transaction = Transaction::factory()->makeOne();

        $response = $this->post(route('api.transaction.store', $transaction->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Transaction Store Endpoint shall forbid users without the create transaction permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $transaction = Transaction::factory()->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.transaction.store', $transaction->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Transaction Store Endpoint shall require amount, date, description & service fields.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(Transaction::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.transaction.store'));

        $response->assertJsonValidationErrors([
            Transaction::ATTRIBUTE_AMOUNT,
            Transaction::ATTRIBUTE_DATE,
            Transaction::ATTRIBUTE_DESCRIPTION,
            Transaction::ATTRIBUTE_SERVICE,
        ]);
    }

    /**
     * The Transaction Store Endpoint shall create a transaction.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $parameters = array_merge(
            Transaction::factory()->raw(),
            [Transaction::ATTRIBUTE_SERVICE => Service::getRandomInstance()->description]
        );

        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(Transaction::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.transaction.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(Transaction::TABLE, 1);
    }
}
