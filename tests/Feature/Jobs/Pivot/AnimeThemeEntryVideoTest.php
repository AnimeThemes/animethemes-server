<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot;

use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class AnimeThemeEntryVideoTest.
 */
class AnimeThemeEntryVideoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When a Video is attached to an AnimeThemeEntry or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnimeThemeEntryVideoCreatedSendsDiscordNotification()
    {
        $video = Video::factory()->createOne();
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $video->animethemeentries()->attach($entry);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a Video is detached from an AnimeThemeEntry or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnimeThemeEntryVideoDeletedSendsDiscordNotification()
    {
        $video = Video::factory()->createOne();
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $video->animethemeentries()->attach($entry);

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $video->animethemeentries()->detach($entry);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
