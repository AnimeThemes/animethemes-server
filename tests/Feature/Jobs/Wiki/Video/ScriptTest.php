<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki\Video;

use App\Constants\FeatureConstants;
use App\Events\Wiki\Video\Script\VideoScriptCreated;
use App\Events\Wiki\Video\Script\VideoScriptDeleted;
use App\Events\Wiki\Video\Script\VideoScriptRestored;
use App\Events\Wiki\Video\Script\VideoScriptUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
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
        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(VideoScriptCreated::class);

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

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(VideoScriptDeleted::class);

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

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(VideoScriptRestored::class);

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

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(VideoScriptUpdated::class);

        $changes = VideoScript::factory()->makeOne();

        $script->fill($changes->getAttributes());
        $script->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
