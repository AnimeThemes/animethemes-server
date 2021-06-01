<?php

declare(strict_types=1);

namespace Jobs;

use App\Jobs\SendDiscordNotification;
use App\Models\Anime;
use App\Models\Entry;
use App\Models\Theme;
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
            ->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        Entry::factory()->for($theme)->create();

        Bus::assertDispatched(SendDiscordNotification::class);
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
            ->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $entry->delete();

        Bus::assertDispatched(SendDiscordNotification::class);
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
            ->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $entry->restore();

        Bus::assertDispatched(SendDiscordNotification::class);
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
            ->create();

        $changes = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->make();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $entry->fill($changes->getAttributes());
        $entry->save();

        Bus::assertDispatched(SendDiscordNotification::class);
    }
}
