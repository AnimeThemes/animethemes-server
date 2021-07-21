<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot;

use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class VideoEntryTest.
 */
class VideoEntryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When a Video is attached to an Entry or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testVideoEntryCreatedSendsDiscordNotification()
    {
        $video = Video::factory()->createOne();
        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->createOne();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $video->entries()->attach($entry);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a Video is detached from an Entry or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testVideoEntryDeletedSendsDiscordNotification()
    {
        $video = Video::factory()->createOne();
        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->createOne();

        $video->entries()->attach($entry);

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $video->entries()->detach($entry);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
