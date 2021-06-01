<?php declare(strict_types=1);

namespace Jobs;

use App\Jobs\SendDiscordNotification;
use App\Models\Anime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class AnimeTest
 * @package Jobs
 */
class AnimeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an anime is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnimeCreatedSendsDiscordNotification()
    {
        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        Anime::factory()->create();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an anime is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnimeDeletedSendsDiscordNotification()
    {
        $anime = Anime::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $anime->delete();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an anime is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnimeRestoredSendsDiscordNotification()
    {
        $anime = Anime::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $anime->restore();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When an anime is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAnimeUpdatedSendsDiscordNotification()
    {
        $anime = Anime::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $changes = Anime::factory()->make();

        $anime->fill($changes->getAttributes());
        $anime->save();

        Bus::assertDispatched(SendDiscordNotification::class);
    }
}
