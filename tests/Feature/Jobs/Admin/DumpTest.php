<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Admin;

use App\Constants\FeatureConstants;
use App\Events\Admin\Dump\DumpCreated;
use App\Events\Admin\Dump\DumpDeleted;
use App\Events\Admin\Dump\DumpUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Admin\Dump;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class DumpTest extends TestCase
{
    /**
     * When a dump is created, a SendDiscordNotification job shall be dispatched.
     */
    public function testDumpCreatedSendsDiscordNotification(): void
    {
        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(DumpCreated::class);

        Dump::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a dump is deleted, a SendDiscordNotification job shall be dispatched.
     */
    public function testDumpDeletedSendsDiscordNotification(): void
    {
        $dump = Dump::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(DumpDeleted::class);

        $dump->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a dump is updated, a SendDiscordNotification job shall be dispatched.
     */
    public function testDumpUpdatedSendsDiscordNotification(): void
    {
        $dump = Dump::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(DumpUpdated::class);

        $changes = Dump::factory()->makeOne();

        $dump->fill($changes->getAttributes());
        $dump->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
