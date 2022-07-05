<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki;

use App\Events\Wiki\Video\VideoCreated;
use App\Events\Wiki\Video\VideoDeleted;
use App\Events\Wiki\Video\VideoRestored;
use App\Events\Wiki\Video\VideoUpdated;
use App\Models\Wiki\Video;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class VideoTest.
 */
class VideoTest extends TestCase
{
    /**
     * When a Video is created, a VideoCreated event shall be dispatched.
     *
     * @return void
     */
    public function testVideoCreatedEventDispatched(): void
    {
        Event::fake();

        Video::factory()->createOne();

        Event::assertDispatched(VideoCreated::class);
    }

    /**
     * When a Video is deleted, a VideoDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testVideoDeletedEventDispatched(): void
    {
        Event::fake();

        $video = Video::factory()->createOne();

        $video->delete();

        Event::assertDispatched(VideoDeleted::class);
    }

    /**
     * When a Video is restored, a VideoRestored event shall be dispatched.
     *
     * @return void
     */
    public function testVideoRestoredEventDispatched(): void
    {
        Event::fake();

        $video = Video::factory()->createOne();

        $video->restore();

        Event::assertDispatched(VideoRestored::class);
    }

    /**
     * When a Video is restored, a VideoUpdated event shall not be dispatched.
     * Note: This is a customization that overrides default framework behavior.
     * An updated event is fired on restore.
     *
     * @return void
     */
    public function testVideoRestoresQuietly(): void
    {
        Event::fake();

        $video = Video::factory()->createOne();

        $video->restore();

        Event::assertNotDispatched(VideoUpdated::class);
    }

    /**
     * When a Video is updated, a VideoUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testVideoUpdatedEventDispatched(): void
    {
        Event::fake();

        $video = Video::factory()->createOne();
        $changes = Video::factory()->makeOne();

        $video->fill($changes->getAttributes());
        $video->save();

        Event::assertDispatched(VideoUpdated::class);
    }

    /**
     * The VideoUpdated event shall contain embed fields.
     *
     * @return void
     */
    public function testVideoUpdatedEventEmbedFields(): void
    {
        Event::fake();

        $anime = Video::factory()->createOne();
        $changes = Video::factory()->makeOne();

        $anime->fill($changes->getAttributes());
        $anime->save();

        Event::assertDispatched(VideoUpdated::class, function (VideoUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }
}
