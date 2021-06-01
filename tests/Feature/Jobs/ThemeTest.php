<?php

declare(strict_types=1);

namespace Jobs;

use App\Jobs\SendDiscordNotification;
use App\Models\Anime;
use App\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class ThemeTest
 * @package Jobs
 */
class ThemeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When a theme is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testThemeCreatedSendsDiscordNotification()
    {
        $anime = Anime::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        Theme::factory()->for($anime)->create();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When a theme is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testThemeDeletedSendsDiscordNotification()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $theme->delete();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When a theme is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testThemeRestoredSendsDiscordNotification()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $theme->restore();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When a theme is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testThemeUpdatedSendsDiscordNotification()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->create();

        $changes = Theme::factory()
            ->for(Anime::factory())
            ->make();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $theme->fill($changes->getAttributes());
        $theme->save();

        Bus::assertDispatched(SendDiscordNotification::class);
    }
}
