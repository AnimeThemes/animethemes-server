<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\List\External;

use App\Constants\FeatureConstants;
use App\Events\List\ExternalProfile\ExternalEntry\ExternalEntryCreated;
use App\Events\List\ExternalProfile\ExternalEntry\ExternalEntryDeleted;
use App\Events\List\ExternalProfile\ExternalEntry\ExternalEntryRestored;
use App\Events\List\ExternalProfile\ExternalEntry\ExternalEntryUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\List\ExternalProfile;
use App\Models\List\External\ExternalEntry;
use App\Models\Wiki\Anime;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

/**
 * Class ExternalEntryTest.
 */
class ExternalEntryTest extends TestCase
{
    /**
     * When an entry is created, a SendDiscordNotification job shall not be dispatched.
     *
     * @return void
     */
    public function testPlaylistCreatedSendsDiscordNotification(): void
    {
        $profile = ExternalProfile::factory()->createOne();
        $anime = Anime::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ExternalEntryCreated::class);

        ExternalEntry::factory()
            ->for($profile)
            ->for($anime)
            ->createOne();

        Bus::assertNotDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an entry is deleted, a SendDiscordNotification job shall not be dispatched.
     *
     * @return void
     */
    public function testPlaylistDeletedSendsDiscordNotification(): void
    {
        $profile = ExternalProfile::factory()->createOne();
        $anime = Anime::factory()->createOne();

        $entry = ExternalEntry::factory()
            ->for($profile)
            ->for($anime)
            ->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ExternalEntryDeleted::class);

        $entry->delete();

        Bus::assertNotDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an entry is restored, a SendDiscordNotification job shall not be dispatched.
     *
     * @return void
     */
    public function testPlaylistRestoredSendsDiscordNotification(): void
    {
        $profile = ExternalProfile::factory()->createOne();
        $anime = Anime::factory()->createOne();

        $entry = ExternalEntry::factory()
            ->for($profile)
            ->for($anime)
            ->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ExternalEntryRestored::class);

        $entry->restore();

        Bus::assertNotDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an entry is updated, a SendDiscordNotification job shall not be dispatched.
     *
     * @return void
     */
    public function testPlaylistUpdatedSendsDiscordNotification(): void
    {
        $profile = ExternalProfile::factory()->createOne();
        $anime = Anime::factory()->createOne();

        $entry = ExternalEntry::factory()
            ->for($profile)
            ->for($anime)
            ->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ExternalEntryUpdated::class);

        $changes = array_merge(
            ExternalEntry::factory()->raw(),
            [ExternalEntry::ATTRIBUTE_ANIME => Anime::factory()->createOne()->getKey()],
        );

        $entry->fill($changes);
        $entry->save();

        Bus::assertNotDispatched(SendDiscordNotificationJob::class);
    }
}
