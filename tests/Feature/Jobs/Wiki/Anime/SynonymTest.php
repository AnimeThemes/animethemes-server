<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki\Anime;

use App\Constants\Config\FlagConstants;
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
    public function testSynonymCreatedSendsDiscordNotification(): void
    {
        $anime = Anime::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        AnimeSynonym::factory()->for($anime)->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a synonym is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSynonymDeletedSendsDiscordNotification(): void
    {
        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $synonym->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a synonym is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSynonymRestoredSendsDiscordNotification(): void
    {
        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $synonym->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a synonym is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSynonymUpdatedSendsDiscordNotification(): void
    {
        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        $changes = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->makeOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $synonym->fill($changes->getAttributes());
        $synonym->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
