<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot\Wiki;

use App\Constants\FeatureConstants;
use App\Events\Pivot\Wiki\AnimeStudio\AnimeStudioCreated;
use App\Events\Pivot\Wiki\AnimeStudio\AnimeStudioDeleted;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
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
    public function test_anime_studio_created_sends_discord_notification(): void
    {
        $anime = Anime::factory()->createOne();
        $studio = Studio::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(AnimeStudioCreated::class);

        $anime->studios()->attach($studio);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an Anime is detached from a Studio or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_anime_studio_deleted_sends_discord_notification(): void
    {
        $anime = Anime::factory()->createOne();
        $studio = Studio::factory()->createOne();

        $anime->studios()->attach($studio);

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(AnimeStudioDeleted::class);

        $anime->studios()->detach($studio);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
