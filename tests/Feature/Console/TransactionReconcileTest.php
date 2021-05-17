<?php

namespace Tests\Feature\Console;

use App\Console\Commands\TransactionReconcileCommand;
use App\Enums\BillingService;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Symfony\Component\Console\Exception\RuntimeException;
use Tests\TestCase;

class TransactionReconcileTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Reconcile Transaction Command shall require a 'service' argument.
     *
     * @return void
     */
    public function testServiceArgumentRequired()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "service").');

        $this->artisan(TransactionReconcileCommand::class)->run();
    }

    /**
     * If no changes are needed, the Reconcile Transaction Command shall output 'No Transaction created or deleted or updated'.
     *
     * @return void
     */
    public function testNoResults()
    {
        $this->artisan(TransactionReconcileCommand::class, ['service' => BillingService::OTHER()->key])->expectsOutput('No Transactions created or deleted or updated');
    }

    /**
     * If transactions are deleted, the Reconcile Transaction Command shall output '0 Transactions created, {Deleted Count} Transactions deleted, 0 Transactions updated'.
     *
     * @return void
     */
    public function testDeleted()
    {
        $deleted_transaction_count = $this->faker->randomDigitNotNull;
        Transaction::factory()->count($deleted_transaction_count)->create([
            'service' => BillingService::OTHER,
        ]);

        $this->artisan(TransactionReconcileCommand::class, ['service' => BillingService::OTHER()->key])->expectsOutput("0 Transactions created, {$deleted_transaction_count} Transactions deleted, 0 Transactions updated");
    }
}
