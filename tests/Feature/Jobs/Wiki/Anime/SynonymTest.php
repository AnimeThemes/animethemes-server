<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki\Anime;

use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class SynonymTest.
 */
class SynonymTest extends TestCase
{
    /**
     * When a synonym is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSynonymCreatedSendsDiscordNotification()
    {
        $anime = Anime::factory()->createOne();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        AnimeSynonym::factory()->for($anime)->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a synonym is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSynonymDeletedSendsDiscordNotification()
    {
        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

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
        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

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
        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        $changes = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->makeOne();

        Config::set('flags.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $synonym->fill($changes->getAttributes());
        $synonym->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
