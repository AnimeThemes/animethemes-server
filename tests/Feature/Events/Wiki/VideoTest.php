<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki;

use App\Events\Wiki\Video\VideoCreated;
use App\Events\Wiki\Video\VideoDeleted;
use App\Events\Wiki\Video\VideoRestored;
use App\Events\Wiki\Video\VideoUpdated;
use App\Models\Wiki\Video;
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
    public function testVideoCreatedEventDispatched()
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
    public function testVideoDeletedEventDispatched()
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
    public function testVideoRestoredEventDispatched()
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
    public function testVideoRestoresQuietly()
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
    public function testVideoUpdatedEventDispatched()
    {
        Event::fake();

        $video = Video::factory()->createOne();
        $changes = Video::factory()->makeOne();

        $video->fill($changes->getAttributes());
        $video->save();

        Event::assertDispatched(VideoUpdated::class);
    }
}
