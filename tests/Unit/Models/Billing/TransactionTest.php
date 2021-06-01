<?php

declare(strict_types=1);

namespace Models\Billing;

use App\Enums\Billing\Service;
use App\Models\Billing\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class TransactionTest.
 */
class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The service attribute of an transaction shall be cast to a Service enum instance.
     *
     * @return void
     */
    public function testCastsServiceToEnum()
    {
        $transaction = Transaction::factory()->create();

        $service = $transaction->service;

        static::assertInstanceOf(Service::class, $service);
    }

    /**
     * Transaction shall be auditable.
     *
     * @return void
     */
    public function testAuditable()
    {
        Config::set('audit.console', true);

        $transaction = Transaction::factory()->create();

        static::assertEquals(1, $transaction->audits->count());
    }

    /**
     * Transactions shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $transaction = Transaction::factory()->create();

        static::assertIsString($transaction->getName());
    }
}
