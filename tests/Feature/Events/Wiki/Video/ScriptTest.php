<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki\Video;

use App\Events\Wiki\Video\Script\VideoScriptCreated;
use App\Events\Wiki\Video\Script\VideoScriptDeleted;
use App\Events\Wiki\Video\Script\VideoScriptRestored;
use App\Events\Wiki\Video\Script\VideoScriptUpdated;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class ScriptTest.
 */
class ScriptTest extends TestCase
{
    /**
     * When a Script is created, a VideoScriptCreated event shall be dispatched.
     *
     * @return void
     */
    public function testVideoScriptCreatedEventDispatched(): void
    {
        VideoScript::factory()->createOne();

        Event::assertDispatched(VideoScriptCreated::class);
    }

    /**
     * When a Script is deleted, a VideoScriptDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testVideoScriptDeletedEventDispatched(): void
    {
        $script = VideoScript::factory()->createOne();

        $script->delete();

        Event::assertDispatched(VideoScriptDeleted::class);
    }

    /**
     * When a Script is restored, a VideoScriptRestored event shall be dispatched.
     *
     * @return void
     */
    public function testVideoScriptRestoredEventDispatched(): void
    {
        $script = VideoScript::factory()->createOne();

        $script->restore();

        Event::assertDispatched(VideoScriptRestored::class);
    }

    /**
     * When a Script is restored, a VideoScriptUpdated event shall not be dispatched.
     * Note: This is a customization that overrides default framework behavior.
     * An updated event is fired on restore.
     *
     * @return void
     */
    public function testVideoScriptRestoresQuietly(): void
    {
        $script = VideoScript::factory()->createOne();

        $script->restore();

        Event::assertNotDispatched(VideoScriptUpdated::class);
    }

    /**
     * When a Script is updated, a VideoScriptUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testVideoScriptUpdatedEventDispatched(): void
    {
        $script = VideoScript::factory()->createOne();
        $changes = VideoScript::factory()->makeOne();

        $script->fill($changes->getAttributes());
        $script->save();

        Event::assertDispatched(VideoScriptUpdated::class);
    }

    /**
     * The VideoScriptUpdated event shall contain embed fields.
     *
     * @return void
     */
    public function testVideoScriptUpdatedEventEmbedFields(): void
    {
        $script = VideoScript::factory()->createOne();
        $changes = VideoScript::factory()->makeOne();

        $script->fill($changes->getAttributes());
        $script->save();

        Event::assertDispatched(VideoScriptUpdated::class, function (VideoScriptUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }
}
