<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki\Anime;

use App\Constants\FeatureConstants;
use App\Events\Wiki\Anime\Synonym\SynonymCreated;
use App\Events\Wiki\Anime\Synonym\SynonymDeleted;
use App\Events\Wiki\Anime\Synonym\SynonymRestored;
use App\Events\Wiki\Anime\Synonym\SynonymUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
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
    public function test_synonym_created_sends_discord_notification(): void
    {
        $anime = Anime::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(SynonymCreated::class);

        AnimeSynonym::factory()->for($anime)->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a synonym is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_synonym_deleted_sends_discord_notification(): void
    {
        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(SynonymDeleted::class);

        $synonym->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a synonym is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_synonym_restored_sends_discord_notification(): void
    {
        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(SynonymRestored::class);

        $synonym->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a synonym is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_synonym_updated_sends_discord_notification(): void
    {
        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        $changes = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->makeOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(SynonymUpdated::class);

        $synonym->fill($changes->getAttributes());
        $synonym->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
