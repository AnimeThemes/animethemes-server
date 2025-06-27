<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki;

use App\Constants\FeatureConstants;
use App\Events\Wiki\Audio\AudioCreated;
use App\Events\Wiki\Audio\AudioDeleted;
use App\Events\Wiki\Audio\AudioRestored;
use App\Events\Wiki\Audio\AudioUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Audio;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

/**
 * Class AudioTest.
 */
class AudioTest extends TestCase
{
    /**
     * When an audio is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_audio_created_sends_discord_notification(): void
    {
        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(AudioCreated::class);

        Audio::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an audio is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_audio_deleted_sends_discord_notification(): void
    {
        $audio = Audio::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(AudioDeleted::class);

        $audio->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an audio is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_audio_restored_sends_discord_notification(): void
    {
        $audio = Audio::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(AudioRestored::class);

        $audio->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an audio is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_audio_updated_sends_discord_notification(): void
    {
        $audio = Audio::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(AudioUpdated::class);

        $changes = Audio::factory()->makeOne();

        $audio->fill($changes->getAttributes());
        $audio->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
