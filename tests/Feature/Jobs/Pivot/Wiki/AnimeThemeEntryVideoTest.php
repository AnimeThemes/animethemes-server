<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot\Wiki;

use App\Constants\FeatureConstants;
use App\Events\Pivot\Wiki\AnimeThemeEntryVideo\AnimeThemeEntryVideoCreated;
use App\Events\Pivot\Wiki\AnimeThemeEntryVideo\AnimeThemeEntryVideoDeleted;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class AnimeThemeEntryVideoTest extends TestCase
{
    /**
     * When a Video is attached to an AnimeThemeEntry or vice versa, a SendDiscordNotification job shall be dispatched.
     */
    public function testAnimeThemeEntryVideoCreatedSendsDiscordNotification(): void
    {
        $video = Video::factory()->createOne();
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(AnimeThemeEntryVideoCreated::class);

        $video->animethemeentries()->attach($entry);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a Video is detached from an AnimeThemeEntry or vice versa, a SendDiscordNotification job shall be dispatched.
     */
    public function testAnimeThemeEntryVideoDeletedSendsDiscordNotification(): void
    {
        $video = Video::factory()->createOne();
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $video->animethemeentries()->attach($entry);

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(AnimeThemeEntryVideoDeleted::class);

        $video->animethemeentries()->detach($entry);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
