<?php

namespace Tests\Feature\Jobs;

use App\Jobs\SendDiscordNotification;
use App\Models\Anime;
use App\Models\ExternalResource;
use App\Pivots\AnimeResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AnimeResourceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * When an Anime is attached to a Resource or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnimeResourceCreatedSendsDiscordNotification()
    {
        $anime = Anime::factory()->create();
        $resource = ExternalResource::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $anime->externalResources()->attach($resource);

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an Anime is detached from a Resource or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnimeResourceDeletedSendsDiscordNotification()
    {
        $anime = Anime::factory()->create();
        $resource = ExternalResource::factory()->create();
        $anime->externalResources()->attach($resource);

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $anime->externalResources()->detach($resource);

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an Anime Resource pivot is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnimeResourceUpdatedSendsDiscordNotification()
    {
        $anime = Anime::factory()->create();
        $resource = ExternalResource::factory()->create();

        $anime_resource = AnimeResource::factory()
            ->for($anime, 'anime')
            ->for($resource, 'resource')
            ->create();

        $changes = AnimeResource::factory()
            ->for($anime, 'anime')
            ->for($resource, 'resource')
            ->make();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $anime_resource->fill($changes->getAttributes());
        $anime_resource->save();

        Bus::assertDispatched(SendDiscordNotification::class);
    }
}
