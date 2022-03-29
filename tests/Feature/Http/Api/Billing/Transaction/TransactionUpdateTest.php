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
 * Class TransactionUpdateTest.
 */
class TransactionUpdateTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Transaction Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $transaction = Transaction::factory()->createOne();

        $parameters = array_merge(
            Transaction::factory()->raw(),
            [Transaction::ATTRIBUTE_SERVICE => Service::getRandomInstance()->description]
        );

        $response = $this->put(route('api.transaction.update', ['transaction' => $transaction] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Transaction Update Endpoint shall update a transaction.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $transaction = Transaction::factory()->createOne();

        $parameters = array_merge(
            Transaction::factory()->raw(),
            [Transaction::ATTRIBUTE_SERVICE => Service::getRandomInstance()->description]
        );

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('admin')->createOne(),
            ['transaction:update']
        );

        $response = $this->put(route('api.transaction.update', ['transaction' => $transaction] + $parameters));

        $response->assertOk();
    }
}
