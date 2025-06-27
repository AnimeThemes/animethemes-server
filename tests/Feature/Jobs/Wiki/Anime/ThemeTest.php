<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki\Anime;

use App\Constants\FeatureConstants;
use App\Events\Wiki\Anime\Theme\ThemeCreated;
use App\Events\Wiki\Anime\Theme\ThemeDeleted;
use App\Events\Wiki\Anime\Theme\ThemeRestored;
use App\Events\Wiki\Anime\Theme\ThemeUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

/**
 * Class ThemeTest.
 */
class ThemeTest extends TestCase
{
    /**
     * When a theme is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_theme_created_sends_discord_notification(): void
    {
        $anime = Anime::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ThemeCreated::class);

        AnimeTheme::factory()->for($anime)->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a theme is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_theme_deleted_sends_discord_notification(): void
    {
        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ThemeDeleted::class);

        $theme->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a theme is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_theme_restored_sends_discord_notification(): void
    {
        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ThemeRestored::class);

        $theme->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a theme is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_theme_updated_sends_discord_notification(): void
    {
        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        $changes = AnimeTheme::factory()
            ->for(Anime::factory())
            ->makeOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ThemeUpdated::class);

        $theme->fill($changes->getAttributes());
        $theme->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
