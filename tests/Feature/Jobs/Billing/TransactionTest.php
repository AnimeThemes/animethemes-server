<?php

declare(strict_types=1);

namespace Jobs\Billing;

use App\Jobs\SendDiscordNotificationJob;
use App\Models\Billing\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class TransactionTest.
 */
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
        Bus::fake(SendDiscordNotificationJob::class);

        Transaction::factory()->create();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
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
        Bus::fake(SendDiscordNotificationJob::class);

        $transaction->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
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
        Bus::fake(SendDiscordNotificationJob::class);

        $transaction->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
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
        Bus::fake(SendDiscordNotificationJob::class);

        $changes = Transaction::factory()->make();

        $transaction->fill($changes->getAttributes());
        $transaction->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
