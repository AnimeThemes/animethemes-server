<?php

namespace Tests\Feature\Jobs;

use App\Jobs\SendDiscordNotification;
use App\Models\Anime;
use App\Models\Entry;
use App\Models\Theme;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class VideoEntryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * When a Video is attached to an Entry or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testVideoEntryCreatedSendsDiscordNotification()
    {
        $video = Video::factory()->create();
        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $video->entries()->attach($entry);

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When a Video is detached from an Entry or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testVideoEntryDeletedSendsDiscordNotification()
    {
        $video = Video::factory()->create();
        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();

        $video->entries()->attach($entry);

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $video->entries()->detach($entry);

        Bus::assertDispatched(SendDiscordNotification::class);
    }
}
