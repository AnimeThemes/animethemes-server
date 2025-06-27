<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki\Anime\Theme;

use App\Constants\FeatureConstants;
use App\Events\Wiki\Anime\Theme\Entry\EntryCreated;
use App\Events\Wiki\Anime\Theme\Entry\EntryDeleted;
use App\Events\Wiki\Anime\Theme\Entry\EntryRestored;
use App\Events\Wiki\Anime\Theme\Entry\EntryUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
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
    public function test_entry_created_sends_discord_notification(): void
    {
        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(EntryCreated::class);

        AnimeThemeEntry::factory()->for($theme)->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an entry is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_entry_deleted_sends_discord_notification(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(EntryDeleted::class);

        $entry->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an entry is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_entry_restored_sends_discord_notification(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(EntryRestored::class);

        $entry->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an entry is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_entry_updated_sends_discord_notification(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $changes = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->makeOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(EntryUpdated::class);

        $entry->fill($changes->getAttributes());
        $entry->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
