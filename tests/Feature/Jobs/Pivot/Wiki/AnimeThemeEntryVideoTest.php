<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot\Wiki;

use App\Constants\Config\FlagConstants;
use App\Events\Pivot\Wiki\AnimeThemeEntryVideo\AnimeThemeEntryVideoCreated;
use App\Events\Pivot\Wiki\AnimeThemeEntryVideo\AnimeThemeEntryVideoDeleted;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class AnimeThemeEntryVideoTest.
 */
class AnimeThemeEntryVideoTest extends TestCase
{
    /**
     * When a Video is attached to an AnimeThemeEntry or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnimeThemeEntryVideoCreatedSendsDiscordNotification(): void
    {
        $video = Video::factory()->createOne();
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(AnimeThemeEntryVideoCreated::class);

        $video->animethemeentries()->attach($entry);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a Video is detached from an AnimeThemeEntry or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnimeThemeEntryVideoDeletedSendsDiscordNotification(): void
    {
        $video = Video::factory()->createOne();
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $video->animethemeentries()->attach($entry);

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(AnimeThemeEntryVideoDeleted::class);

        $video->animethemeentries()->detach($entry);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
