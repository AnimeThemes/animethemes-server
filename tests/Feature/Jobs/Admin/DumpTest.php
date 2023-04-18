<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Admin;

use App\Constants\Config\FlagConstants;
use App\Events\Admin\Dump\DumpCreated;
use App\Events\Admin\Dump\DumpDeleted;
use App\Events\Admin\Dump\DumpRestored;
use App\Events\Admin\Dump\DumpUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Admin\Dump;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class DumpTest.
 */
class DumpTest extends TestCase
{
    /**
     * When a dump is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testDumpCreatedSendsDiscordNotification(): void
    {
        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(DumpCreated::class);

        Dump::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a dump is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testDumpDeletedSendsDiscordNotification(): void
    {
        $dump = Dump::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(DumpDeleted::class);

        $dump->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a dump is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testDumpRestoredSendsDiscordNotification(): void
    {
        $dump = Dump::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(DumpRestored::class);

        $dump->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a dump is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testDumpUpdatedSendsDiscordNotification(): void
    {
        $dump = Dump::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(DumpUpdated::class);

        $changes = Dump::factory()->makeOne();

        $dump->fill($changes->getAttributes());
        $dump->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
