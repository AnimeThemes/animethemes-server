<?php

declare(strict_types=1);

namespace Jobs\Billing;

use App\Jobs\SendDiscordNotification;
use App\Models\Billing\Balance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class BalanceTest
 * @package Jobs\Billing
 */
class BalanceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an balance is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testBalanceCreatedSendsDiscordNotification()
    {
        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        Balance::factory()->create();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an balance is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testBalanceDeletedSendsDiscordNotification()
    {
        $balance = Balance::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $balance->delete();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an balance is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testBalanceRestoredSendsDiscordNotification()
    {
        $balance = Balance::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $balance->restore();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an balance is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testBalanceUpdatedSendsDiscordNotification()
    {
        $balance = Balance::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $changes = Balance::factory()->make();

        $balance->fill($changes->getAttributes());
        $balance->save();

        Bus::assertDispatched(SendDiscordNotification::class);
    }
}
