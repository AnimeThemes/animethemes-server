<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Billing;

use App\Constants\Config\FlagConstants;
use App\Events\Billing\Balance\BalanceCreated;
use App\Events\Billing\Balance\BalanceDeleted;
use App\Events\Billing\Balance\BalanceRestored;
use App\Events\Billing\Balance\BalanceUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Billing\Balance;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class BalanceTest.
 */
class BalanceTest extends TestCase
{
    /**
     * When a balance is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testBalanceCreatedSendsDiscordNotification(): void
    {
        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(BalanceCreated::class);

        Balance::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a balance is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testBalanceDeletedSendsDiscordNotification(): void
    {
        $balance = Balance::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(BalanceDeleted::class);

        $balance->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a balance is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testBalanceRestoredSendsDiscordNotification(): void
    {
        $balance = Balance::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(BalanceRestored::class);

        $balance->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a balance is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testBalanceUpdatedSendsDiscordNotification(): void
    {
        $balance = Balance::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(BalanceUpdated::class);

        $changes = Balance::factory()->makeOne();

        $balance->fill($changes->getAttributes());
        $balance->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
