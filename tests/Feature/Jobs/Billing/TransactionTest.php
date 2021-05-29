<?php

namespace Tests\Feature\Jobs\Billing;

use App\Jobs\SendDiscordNotification;
use App\Models\Billing\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an transaction is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testTransactionCreatedSendsDiscordNotification()
    {
        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        Transaction::factory()->create();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an transaction is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testTransactionDeletedSendsDiscordNotification()
    {
        $transaction = Transaction::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $transaction->delete();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an transaction is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testTransactionRestoredSendsDiscordNotification()
    {
        $transaction = Transaction::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $transaction->restore();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an transaction is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testTransactionUpdatedSendsDiscordNotification()
    {
        $transaction = Transaction::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $changes = Transaction::factory()->make();

        $transaction->fill($changes->getAttributes());
        $transaction->save();

        Bus::assertDispatched(SendDiscordNotification::class);
    }
}
