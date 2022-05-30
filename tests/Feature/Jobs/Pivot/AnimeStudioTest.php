<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot;

use App\Constants\Config\FlagConstants;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class AnimeStudioTest.
 */
class AnimeStudioTest extends TestCase
{
    /**
     * When an Anime is attached to a Studio or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnimeStudioCreatedSendsDiscordNotification(): void
    {
        $anime = Anime::factory()->createOne();
        $studio = Studio::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $anime->studios()->attach($studio);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an Anime is detached from a Studio or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnimeStudioDeletedSendsDiscordNotification(): void
    {
        $anime = Anime::factory()->createOne();
        $studio = Studio::factory()->createOne();

        $anime->studios()->attach($studio);

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $anime->studios()->detach($studio);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
