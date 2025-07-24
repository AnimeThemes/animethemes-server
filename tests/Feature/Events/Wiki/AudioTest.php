<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki;

use App\Events\Wiki\Audio\AudioCreated;
use App\Events\Wiki\Audio\AudioDeleted;
use App\Events\Wiki\Audio\AudioRestored;
use App\Events\Wiki\Audio\AudioUpdated;
use App\Models\Wiki\Audio;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AudioTest extends TestCase
{
    /**
     * When an Audio is created, an AudioCreated event shall be dispatched.
     */
    public function testAudioCreatedEventDispatched(): void
    {
        Audio::factory()->createOne();

        Event::assertDispatched(AudioCreated::class);
    }

    /**
     * When an Audio is deleted, an AudioDeleted event shall be dispatched.
     */
    public function testAudioDeletedEventDispatched(): void
    {
        $audio = Audio::factory()->createOne();

        $audio->delete();

        Event::assertDispatched(AudioDeleted::class);
    }

    /**
     * When an Audio is restored, an AudioRestored event shall be dispatched.
     */
    public function testAudioRestoredEventDispatched(): void
    {
        $audio = Audio::factory()->createOne();

        $audio->restore();

        Event::assertDispatched(AudioRestored::class);
    }

    /**
     * When an Audio is restored, an AudioUpdated event shall not be dispatched.
     * Note: This is a customization that overrides default framework behavior.
     * An updated event is fired on restore.
     */
    public function testAudioRestoresQuietly(): void
    {
        $audio = Audio::factory()->createOne();

        $audio->restore();

        Event::assertNotDispatched(AudioUpdated::class);
    }

    /**
     * When an Audio is updated, an AudioUpdated event shall be dispatched.
     */
    public function testAudioUpdatedEventDispatched(): void
    {
        $audio = Audio::factory()->createOne();
        $changes = Audio::factory()->makeOne();

        $audio->fill($changes->getAttributes());
        $audio->save();

        Event::assertDispatched(AudioUpdated::class);
    }

    /**
     * The AudioUpdated event shall contain embed fields.
     */
    public function testAudioUpdatedEventEmbedFields(): void
    {
        $audio = Audio::factory()->createOne();
        $changes = Audio::factory()->makeOne();

        $audio->fill($changes->getAttributes());
        $audio->save();

        Event::assertDispatched(AudioUpdated::class, function (AudioUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }
}
