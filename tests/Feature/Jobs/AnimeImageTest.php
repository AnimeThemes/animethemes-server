<?php

namespace Tests\Feature\Jobs;

use App\Jobs\SendDiscordNotification;
use App\Models\Anime;
use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AnimeImageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an Anime is attached to an Image or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnimeImageCreatedSendsDiscordNotification()
    {
        $anime = Anime::factory()->create();
        $image = Image::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $anime->images()->attach($image);

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an Anime is detached from an Image or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnimeImageDeletedSendsDiscordNotification()
    {
        $anime = Anime::factory()->create();
        $image = Image::factory()->create();
        $anime->images()->attach($image);

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $anime->images()->detach($image);

        Bus::assertDispatched(SendDiscordNotification::class);
    }
}
