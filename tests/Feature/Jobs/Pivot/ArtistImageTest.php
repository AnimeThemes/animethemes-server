<?php

namespace Tests\Feature\Jobs\Pivot;

use App\Jobs\SendDiscordNotification;
use App\Models\Artist;
use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ArtistImageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an Artist is attached to an Image or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistImageCreatedSendsDiscordNotification()
    {
        $artist = Artist::factory()->create();
        $image = Image::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $artist->images()->attach($image);

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an Artist is detached from an Image or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testArtistImageDeletedSendsDiscordNotification()
    {
        $artist = Artist::factory()->create();
        $image = Image::factory()->create();
        $artist->images()->attach($image);

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $artist->images()->detach($image);

        Bus::assertDispatched(SendDiscordNotification::class);
    }
}
