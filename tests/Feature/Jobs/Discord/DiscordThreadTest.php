<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Discord;

use App\Constants\FeatureConstants;
use App\Events\Discord\DiscordThread\DiscordThreadDeleted;
use App\Events\Discord\DiscordThread\DiscordThreadUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Discord\DiscordThread;
use App\Models\Wiki\Anime;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class DiscordThreadTest extends TestCase
{
    /**
     * When a thread is deleted, a SendDiscordNotification job shall be dispatched.
     */
    public function testThreadDeletedSendsDiscordNotification(): void
    {
        $thread = DiscordThread::factory()
            ->for(Anime::factory())
            ->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Http::fake();
        Event::fakeExcept(DiscordThreadDeleted::class);

        $thread->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a thread is updated, a SendDiscordNotification job shall be dispatched.
     */
    public function testThreadUpdatedSendsDiscordNotification(): void
    {
        $thread = DiscordThread::factory()
            ->for(Anime::factory())
            ->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Http::fake();
        Event::fakeExcept(DiscordThreadUpdated::class);

        $changes = DiscordThread::factory()
            ->for(Anime::factory())
            ->makeOne();

        $thread->fill($changes->getAttributes());
        $thread->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
