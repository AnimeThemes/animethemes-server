<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Billing;

use App\Jobs\SendDiscordNotificationJob;
use App\Models\Billing\Balance;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class BalanceTest.
 */
class BalanceTest extends TestCase
{

    /**
     * When an balance is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testBalanceCreatedSendsDiscordNotification()
    {
        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        Balance::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an balance is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testBalanceDeletedSendsDiscordNotification()
    {
        $balance = Balance::factory()->createOne();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $balance->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an balance is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testBalanceRestoredSendsDiscordNotification()
    {
        $balance = Balance::factory()->createOne();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $balance->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an balance is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testBalanceUpdatedSendsDiscordNotification()
    {
        $balance = Balance::factory()->createOne();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $changes = Balance::factory()->makeOne();

        $balance->fill($changes->getAttributes());
        $balance->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
