<?php declare(strict_types=1);

namespace Jobs;

use App\Jobs\SendDiscordNotification;
use App\Models\Anime;
use App\Models\Synonym;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class SynonymTest
 * @package Jobs
 */
class SynonymTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When a synonym is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSynonymCreatedSendsDiscordNotification()
    {
        $anime = Anime::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        Synonym::factory()->for($anime)->create();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When a synonym is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSynonymDeletedSendsDiscordNotification()
    {
        $synonym = Synonym::factory()
            ->for(Anime::factory())
            ->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $synonym->delete();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When a synonym is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSynonymRestoredSendsDiscordNotification()
    {
        $synonym = Synonym::factory()
            ->for(Anime::factory())
            ->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $synonym->restore();

        Bus::assertDispatched(SendDiscordNotification::class);
    }

    /**
     * When a synonym is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSynonymUpdatedSendsDiscordNotification()
    {
        $synonym = Synonym::factory()
            ->for(Anime::factory())
            ->create();

        $changes = Synonym::factory()
            ->for(Anime::factory())
            ->make();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotification::class);

        $synonym->fill($changes->getAttributes());
        $synonym->save();

        Bus::assertDispatched(SendDiscordNotification::class);
    }
}
