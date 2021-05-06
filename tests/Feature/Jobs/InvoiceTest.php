<?php

namespace Tests\Feature\Jobs;

use App\Jobs\SendDiscordNotification;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an invoice is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testInvoiceCreatedSendsDiscordNotification()
    {
        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        Invoice::factory()->create();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an invoice is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testInvoiceDeletedSendsDiscordNotification()
    {
        $invoice = Invoice::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $invoice->delete();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an invoice is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testInvoiceRestoredSendsDiscordNotification()
    {
        $invoice = Invoice::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $invoice->restore();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an invoice is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testInvoiceUpdatedSendsDiscordNotification()
    {
        $invoice = Invoice::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $changes = Invoice::factory()->make();

        $invoice->fill($changes->getAttributes());
        $invoice->save();

        Bus::assertDispatched(SendDiscordNotification::class);
    }
}
