<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot;

use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\AnimeResource;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class AnimeResourceTest.
 */
class AnimeResourceTest extends TestCase
{
    /**
     * When an Anime is attached to a Resource or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnimeResourceCreatedSendsDiscordNotification()
    {
        $anime = Anime::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $anime->resources()->attach($resource);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an Anime is detached from a Resource or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnimeResourceDeletedSendsDiscordNotification()
    {
        $anime = Anime::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $anime->resources()->attach($resource);

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $anime->resources()->detach($resource);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an Anime Resource pivot is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnimeResourceUpdatedSendsDiscordNotification()
    {
        $anime = Anime::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $animeResource = AnimeResource::factory()
            ->for($anime, 'anime')
            ->for($resource, 'resource')
            ->createOne();

        $changes = AnimeResource::factory()
            ->for($anime, 'anime')
            ->for($resource, 'resource')
            ->makeOne();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $animeResource->fill($changes->getAttributes());
        $animeResource->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
