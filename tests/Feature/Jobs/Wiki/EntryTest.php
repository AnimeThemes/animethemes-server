<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki;

use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\Theme;
use App\Models\Wiki\Anime\Theme\Entry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class EntryTest.
 */
class EntryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an entry is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testEntryCreatedSendsDiscordNotification()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->createOne();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        Entry::factory()->for($theme)->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an entry is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testEntryDeletedSendsDiscordNotification()
    {
        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->createOne();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $entry->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an entry is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testEntryRestoredSendsDiscordNotification()
    {
        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->createOne();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $entry->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an entry is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testEntryUpdatedSendsDiscordNotification()
    {
        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->createOne();

        $changes = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->makeOne();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $entry->fill($changes->getAttributes());
        $entry->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
