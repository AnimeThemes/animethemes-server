<?php

namespace Tests\Feature\Console;

use App\Console\Commands\InvoiceReconcileCommand;
use App\Enums\InvoiceVendor;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Symfony\Component\Console\Exception\RuntimeException;
use Tests\TestCase;

class InvoiceReconcileTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Reconcile Invoice Command shall require a 'vendor' argument.
     *
     * @return void
     */
    public function testVendorArgumentRequired()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "vendor").');

        $this->artisan(InvoiceReconcileCommand::class)->run();
    }

    /**
     * If no changes are needed, the Reconcile Invoice Command shall output 'No Invoices created or deleted or updated'.
     *
     * @return void
     */
    public function testNoResults()
    {
        $this->artisan(InvoiceReconcileCommand::class, ['vendor' => InvoiceVendor::OTHER()->key])->expectsOutput('No Invoices created or deleted or updated');
    }

    /**
     * If videos are deleted, the Reconcile Invoice Command shall output '0 Invoices created, {Deleted Count} Invoices deleted, 0 Invoices updated'.
     *
     * @return void
     */
    public function testDeleted()
    {
        $deleted_invoice_count = $this->faker->randomDigitNotNull;
        Invoice::factory()->count($deleted_invoice_count)->create([
            'vendor' => InvoiceVendor::OTHER,
        ]);

        $this->artisan(InvoiceReconcileCommand::class, ['vendor' => InvoiceVendor::OTHER()->key])->expectsOutput("0 Invoices created, {$deleted_invoice_count} Invoices deleted, 0 Invoices updated");
    }
}
