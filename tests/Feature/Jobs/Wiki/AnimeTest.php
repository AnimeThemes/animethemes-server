<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki;

use App\Constants\FeatureConstants;
use App\Events\Wiki\Anime\AnimeCreated;
use App\Events\Wiki\Anime\AnimeDeleted;
use App\Events\Wiki\Anime\AnimeRestored;
use App\Events\Wiki\Anime\AnimeUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

/**
 * Class AnimeTest.
 */
class AnimeTest extends TestCase
{
    /**
     * When an anime is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_anime_created_sends_discord_notification(): void
    {
        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(AnimeCreated::class);

        Anime::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an anime is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_anime_deleted_sends_discord_notification(): void
    {
        $anime = Anime::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(AnimeDeleted::class);

        $anime->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an anime is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_anime_restored_sends_discord_notification(): void
    {
        $anime = Anime::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(AnimeRestored::class);

        $anime->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an anime is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_anime_updated_sends_discord_notification(): void
    {
        $anime = Anime::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(AnimeUpdated::class);

        $changes = Anime::factory()->makeOne();

        $anime->fill($changes->getAttributes());
        $anime->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
