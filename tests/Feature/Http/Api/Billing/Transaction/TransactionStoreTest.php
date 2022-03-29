<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Billing\Transaction;

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

        $response->assertForbidden();
    }

    /**
     * The Transaction Store Endpoint shall require name, season, slug & year fields.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        Sanctum::actingAs(
            User::factory()->withCurrentTeam('admin')->createOne(),
            ['transaction:create']
        );

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

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('admin')->createOne(),
            ['transaction:create']
        );

        $response = $this->post(route('api.transaction.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(Transaction::TABLE, 1);
    }
}
