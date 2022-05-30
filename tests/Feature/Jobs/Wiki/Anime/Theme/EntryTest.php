<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki\Anime\Theme;

use App\Constants\Config\FlagConstants;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class EntryTest.
 */
class EntryTest extends TestCase
{
    /**
     * When an entry is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testEntryCreatedSendsDiscordNotification(): void
    {
        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        AnimeThemeEntry::factory()->for($theme)->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an entry is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testEntryDeletedSendsDiscordNotification(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $entry->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an entry is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testEntryRestoredSendsDiscordNotification(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $entry->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an entry is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testEntryUpdatedSendsDiscordNotification(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $changes = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->makeOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $entry->fill($changes->getAttributes());
        $entry->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
