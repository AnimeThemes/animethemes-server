<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki;

use App\Constants\Config\FlagConstants;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Audio;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
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
    public function testAudioCreatedSendsDiscordNotification(): void
    {
        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        Audio::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an audio is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAudioDeletedSendsDiscordNotification(): void
    {
        $audio = Audio::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $audio->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an audio is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAudioRestoredSendsDiscordNotification(): void
    {
        $audio = Audio::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $audio->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an audio is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testAudioUpdatedSendsDiscordNotification(): void
    {
        $audio = Audio::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $changes = Audio::factory()->makeOne();

        $audio->fill($changes->getAttributes());
        $audio->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
