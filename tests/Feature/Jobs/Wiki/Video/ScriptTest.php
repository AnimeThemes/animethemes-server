<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki\Video;

use App\Constants\Config\FlagConstants;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class ScriptTest.
 */
class ScriptTest extends TestCase
{
    /**
     * When a script is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testVideoCreatedSendsDiscordNotification(): void
    {
        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        VideoScript::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a script is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testVideoDeletedSendsDiscordNotification(): void
    {
        $script = VideoScript::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $script->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a script is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testVideoRestoredSendsDiscordNotification(): void
    {
        $script = VideoScript::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $script->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a script is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testVideoUpdatedSendsDiscordNotification(): void
    {
        $script = VideoScript::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $changes = VideoScript::factory()->makeOne();

        $script->fill($changes->getAttributes());
        $script->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
