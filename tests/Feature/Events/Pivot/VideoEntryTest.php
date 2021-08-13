<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Pivot;

use App\Events\Pivot\VideoEntry\VideoEntryCreated;
use App\Events\Pivot\VideoEntry\VideoEntryDeleted;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\Theme\Entry;
use App\Models\Wiki\Anime\Theme;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class VideoEntryTest.
 */
class VideoEntryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When a Video is attached to an Entry or vice versa, a VideoEntryCreated event shall be dispatched.
     *
     * @return void
     */
    public function testVideoEntryCreatedEventDispatched()
    {
        Event::fake(VideoEntryCreated::class);

        $video = Video::factory()->createOne();
        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->createOne();

        $video->entries()->attach($entry);

        Event::assertDispatched(VideoEntryCreated::class);
    }

    /**
     * When a Video is detached from an Entry or vice versa, a VideoEntryDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testVideoEntryDeletedEventDispatched()
    {
        Event::fake(VideoEntryDeleted::class);

        $video = Video::factory()->createOne();
        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->createOne();

        $video->entries()->attach($entry);
        $video->entries()->detach($entry);

        Event::assertDispatched(VideoEntryDeleted::class);
    }
}
