<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki\Anime;

use App\Constants\Config\FlagConstants;
use App\Events\Wiki\Anime\Theme\ThemeCreated;
use App\Events\Wiki\Anime\Theme\ThemeDeleted;
use App\Events\Wiki\Anime\Theme\ThemeRestored;
use App\Events\Wiki\Anime\Theme\ThemeUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
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
    public function testThemeCreatedSendsDiscordNotification(): void
    {
        $anime = Anime::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
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
    public function testThemeDeletedSendsDiscordNotification(): void
    {
        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
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
    public function testThemeRestoredSendsDiscordNotification(): void
    {
        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
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
    public function testThemeUpdatedSendsDiscordNotification(): void
    {
        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        $changes = AnimeTheme::factory()
            ->for(Anime::factory())
            ->makeOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ThemeUpdated::class);

        $theme->fill($changes->getAttributes());
        $theme->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
