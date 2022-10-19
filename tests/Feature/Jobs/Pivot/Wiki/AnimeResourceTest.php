<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot\Wiki;

use App\Constants\Config\FlagConstants;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\AnimeResource;
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
    public function testAnimeResourceCreatedSendsDiscordNotification(): void
    {
        $anime = Anime::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $anime->resources()->attach($resource);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an Anime is detached from a Resource or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnimeResourceDeletedSendsDiscordNotification(): void
    {
        $anime = Anime::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $anime->resources()->attach($resource);

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $anime->resources()->detach($resource);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an Anime Resource pivot is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnimeResourceUpdatedSendsDiscordNotification(): void
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

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $animeResource->fill($changes->getAttributes());
        $animeResource->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
