<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki;

use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Synonym;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class SynonymTest.
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

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        Synonym::factory()->for($anime)->create();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
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

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $synonym->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
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

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $synonym->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
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

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $synonym->fill($changes->getAttributes());
        $synonym->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}