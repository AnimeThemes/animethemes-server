<?php

namespace Tests\Unit\Models;

use App\Enums\BillingService;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The service attribute of an transaction shall be cast to a BillingService enum instance.
     *
     * @return void
     */
    public function testCastsServiceToEnum()
    {
        $transaction = Transaction::factory()->create();

        $service = $transaction->service;

        $this->assertInstanceOf(BillingService::class, $service);
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

        $this->assertEquals(1, $transaction->audits->count());
    }

    /**
     * Transactions shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $transaction = Transaction::factory()->create();

        $this->assertIsString($transaction->getName());
    }
}
