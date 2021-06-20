<?php

declare(strict_types=1);

namespace Events\Wiki;

use App\Events\Wiki\Video\VideoCreated;
use App\Events\Wiki\Video\VideoDeleted;
use App\Events\Wiki\Video\VideoRestored;
use App\Events\Wiki\Video\VideoUpdated;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class VideoTest.
 */
class VideoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When a Video is created, a VideoCreated event shall be dispatched.
     *
     * @return void
     */
    public function testVideoCreatedEventDispatched()
    {
        Event::fake();

        Video::factory()->create();

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

        $video = Video::factory()->create();

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

        $video = Video::factory()->create();

        $video->restore();

        Event::assertDispatched(VideoRestored::class);
    }

    /**
     * When a Video is updated, a VideoUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testVideoUpdatedEventDispatched()
    {
        Event::fake();

        $video = Video::factory()->create();
        $changes = Video::factory()->make();

        $video->fill($changes->getAttributes());
        $video->save();

        Event::assertDispatched(VideoUpdated::class);
    }
}
